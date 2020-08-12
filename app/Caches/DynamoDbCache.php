<?php

declare(strict_types=1);

namespace App\Caches;

use AsyncAws\Core\AwsClientFactory;
use AsyncAws\DynamoDb\DynamoDbClient;
use AsyncAws\DynamoDb\Input\BatchGetItemInput;
use AsyncAws\DynamoDb\Input\BatchWriteItemInput;
use AsyncAws\DynamoDb\Input\CreateTableInput;
use AsyncAws\DynamoDb\Input\DescribeTableInput;
use AsyncAws\DynamoDb\Input\DeleteItemInput;
use AsyncAws\DynamoDb\Input\GetItemInput;
use AsyncAws\DynamoDb\Input\PutItemInput;
use AsyncAws\DynamoDb\Input\ScanInput;
use AsyncAws\DynamoDb\ValueObject\AttributeDefinition;
use AsyncAws\DynamoDb\ValueObject\AttributeValue;
use AsyncAws\DynamoDb\ValueObject\KeySchemaElement;
use AsyncAws\DynamoDb\ValueObject\ProvisionedThroughput;
use DateInterval;
use DateTime;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Traversable;

class DynamoDbCache implements CacheInterface
{
    private string $tableName;
    private LoggerInterface $logger;

    private DynamoDbClient $client;

    public function __construct(
        string $tableName,
        LoggerInterface $logger,
        AwsClientFactory $factory
    ) {
        $this->tableName = $tableName;
        $this->logger = $logger;
        $this->client = $factory->dynamoDb();
    }

    public function get($key, $default = null)
    {
        $this->validateKey($key);

        $result = $this->client->getItem(
            new GetItemInput(
                [
                    'TableName' => $this->tableName,
                    'Key' => [
                        'key' => new AttributeValue(['S' => $key]),
                    ],
                ]
            )
        );

        $item = $result->getItem();

        if ($this->isHit($item)) {
            return $this->decode($item['value']->getB());
        }

        return $default;
    }

    public function set($key, $value, $ttl = null)
    {
        if (is_int($ttl) && $ttl <= 0) {
            return $this->delete($key);
        }

        $this->validateKey($key);

        $item = [
            'key' => new AttributeValue(['S' => $key]),
            'value' => new AttributeValue(['B' => $this->encode($value)]),
        ];

        if ($expiresAt = $this->calculateExpiresAt($ttl)) {
            $item['expires_at'] = new AttributeValue(['N' => $expiresAt]);
        }

        $this->client->putItem(
            new PutItemInput(
                [
                    'TableName' => $this->tableName,
                    'Item' => $item,
                ]
            )
        );

        return true;
    }

    public function delete($key)
    {
        $this->validateKey($key);

        $this->client->deleteItem(
            new DeleteItemInput(
                [
                    'TableName' => $this->tableName,
                    'Key' => [
                        'key' => new AttributeValue(['S' => $key]),
                    ]
                ]
            )
        );

        return true;
    }

    public function clear()
    {
        $result = $this->client->scan(
            new ScanInput(
                [
                    'TableName' => $this->tableName,
                    'AttributesToGet' => [
                        'key',
                    ],
                ]
            )
        );

        $requestItems = [
            $this->tableName => array_map(
                function ($item) {
                    return [
                        'DeleteRequest' => [
                            'Key' => [
                                'key' => $item['key'],
                            ],
                        ]
                    ];
                },
                $result->getItems()
            ),
        ];

        if ($requestItems) {
            do {
                $result = $this->client->batchWriteItem(
                    new BatchWriteItemInput(['RequestItems' => $requestItems])
                );

                $requestItems = $result->getUnprocessedItems();
            } while ($requestItems);
        }

        return true;
    }

    public function getMultiple($keys, $default = null)
    {
        $this->validateKeys($keys);

        $keyValues = array_map(
            function ($key) {
                return [
                    'key' => new AttributeValue(['S' => $key]),
                ];
            },
            $keys
        );

        $responses = [];

        if ($keyValues) {
            $requestItems = [
                $this->tableName => [
                    'Keys' => $keyValues,
                ],
            ];

            do {
                $result = $this->client->batchGetItem(
                    new BatchGetItemInput(['RequestItems' => $requestItems])
                );

                $responses = array_merge(
                    $responses,
                    $result->getResponses()
                );

                $requestItems = $result->getUnprocessedKeys();
            } while ($requestItems);
        }

        $responses = $responses[$this->tableName] ?? [];
        $now = (new DateTime())->getTimestamp();

        $responsesByKey = [];
        foreach ($responses as $response) {
            if ($this->isHit($response)) {
                $responsesByKey[$response['key']->getS()] = $this->decode($response['value']->getB());
            }
        }

        $values = [];
        foreach ($keys as $key) {
            $values[$key] = isset($responsesByKey[$key])
                ? $responsesByKey[$key]
                : $default;
        }

        return $values;
    }

    public function setMultiple($values, $ttl = null)
    {
        if (is_int($ttl) && $ttl <= 0) {
            return $this->deleteMultiple(array_keys($values));
        }

        $this->validateValues($values);
        $expiresAt = $this->calculateExpiresAt($ttl);

        $requestItems = [
            $this->tableName => array_map(
                function ($key) use ($expiresAt, $values) {
                    $item = [
                        'key' => new AttributeValue(['S' => $key]),
                        'value' => new AttributeValue(['B' => $this->encode($values[$key])]),
                    ];

                    if ($expiresAt) {
                        $item['expires_at'] = new AttributeValue(['N' => $expiresAt]);
                    }

                    return [
                        'PutRequest' => [
                            'Item' => $item,
                        ]
                    ];
                },
                array_keys($values)
            ),
        ];

        if ($requestItems) {
            do {
                $result = $this->client->batchWriteItem(
                    new BatchWriteItemInput(['RequestItems' => $requestItems])
                );

                $requestItems = $result->getUnprocessedItems();
            } while ($requestItems);
        }

        return true;
    }

    public function deleteMultiple($keys)
    {
        $this->validateKeys($keys);

        $requestItems = [
            $this->tableName => array_map(
                function ($key) {
                    return [
                        'DeleteRequest' => [
                            'Key' => [
                                'key' => new AttributeValue(['S' => $key]),
                            ],
                        ]
                    ];
                },
                $keys
            ),
        ];

        do {
            $result = $this->client->batchWriteItem(
                new BatchWriteItemInput(['RequestItems' => $requestItems])
            );

            $requestItems = $result->getUnprocessedItems();
        } while ($requestItems);

        return true;
    }

    public function has($key)
    {
        $this->validateKey($key);

        $result = $this->client->getItem(
            new GetItemInput(
                [
                    'TableName' => $this->tableName,
                    'Key' => [
                        'key' => new AttributeValue(['S' => $key]),
                    ],
                    'AttributesToGet' => [
                        'key',
                        'expires_at',
                    ],
                ]
            )
        );

        return $this->isHit($result->getItem());
    }

    /**
     * @param mixed $value
     */
    private function encode($value): string
    {
        return gzcompress(serialize($value));
    }

    /**
     * @return mixed
     */
    private function decode(string $value)
    {
        return unserialize(gzuncompress(base64_decode($value)));
    }

    /**
     * @param mixed $key
     */
    private function validateKey($key): void
    {
        if (
            is_string($key)
            && preg_match('/^[A-Za-z0-9_\.]+$/', $key)
        ) {
            return;
        }

        throw new InvalidArgumentException('Key argument is invalid: ' . json_encode($key));
    }

    /**
     * @param mixed $keys
     */
    private function validateKeys($keys): void
    {
        if (
            is_array($keys)
            || $keys instanceof Traversable
        ) {
            foreach ($keys as $key) {
                $this->validateKey($key);
            }
        } else {
            throw new InvalidArgumentException('Keys argument is invalid: ' . json_encode($keys));
        }
    }

    /**
     * @param mixed $ttl
     */
    private function validateTtl($ttl): void
    {
        if (
            is_int($ttl)
            || $ttl instanceof DateInterval
        ) {
            return;
        }

        throw new InvalidArgumentException('TTL argument is invalid: ' . json_encode($ttl));
    }

    /**
     * @param mixed $values
     */
    private function validateValues($values): void
    {
        if (
            is_array($values)
            || $values instanceof Traversable
        ) {
            foreach (array_keys($values) as $key) {
                $this->validateKey($key);
            }
        } else {
            throw new InvalidArgumentException('Values argument is invalid: ' . json_encode($values));
        }
    }

    /**
     * Inappropriately named, DynamoDB ttl attribute is actually a timestamp since Epoch (in seconds).
     *
     * @param mixed $ttl
     *
     * @return ?string DynamoDB expects N datatype to be sent in string format to avoid loss of precision.
     *                 https://docs.aws.amazon.com/amazondynamodb/latest/developerguide/Programming.LowLevelAPI.html#Programming.LowLevelAPI.Numbers
     */
    private function calculateExpiresAt($ttl): ?string
    {
        if (is_null($ttl)) {
            return null;
        }

        $this->validateTtl($ttl);

        if (is_int($ttl)) {
            $ttl = new DateInterval(sprintf('PT%dS', $ttl));
        }

        return (new DateTime())
            ->add($ttl)
            ->format('U');
    }

    private function isHit(array $item): bool
    {
        if (!$item) {
            return false;
        }

        $now = (new DateTime())->getTimestamp();

        return !isset($item['expires_at'])
            || $item['expires_at']->getN() > $now;
    }
}
