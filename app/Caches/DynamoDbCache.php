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
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

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
        $result = $this->client->getItem(
            new GetItemInput(
                [
                    'TableName' => $this->tableName,
                    'Key' => [
                        'id' => new AttributeValue(['S' => $key]),
                    ],
                ]
            )
        );

        if ($item = $result->getItem()) {
            return $this->decode($item['value']->getB());
        }

        return $default;
    }

    public function set($key, $value, $ttl = null)
    {
        $this->client->putItem(
            new PutItemInput(
                [
                    'TableName' => $this->tableName,
                    'Item' => [
                        'id' => new AttributeValue(['S' => $key]),
                        'value' => new AttributeValue(['B' => $this->encode($value)]),
                    ]
                ]
            )
        );

        return true;
    }

    public function delete($key)
    {
        $this->client->putItem(
            new DeleteItemInput(
                [
                    'TableName' => $this->tableName,
                    'Key' => [
                        'id' => new AttributeValue(['S' => $key]),
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
                        'id',
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
                                'id' => $item['id'],
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
        $keyValues = array_map(
            function ($key) {
                return [
                    'id' => new AttributeValue(['S' => $key]),
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

        $responsesByKey = [];
        foreach ($responses as $response) {
            $responsesByKey[$response['id']->getS()] = $this->decode($response['value']->getB());
        }

        $values = [];
        foreach ($keys as $key) {
            if (isset($responsesByKey[$key])) {
                $values[$key] = $responsesByKey[$key];
            } else {
                $values[$key] = $default;
            }
        }

        return $values;
    }

    public function setMultiple($values, $ttl = null)
    {
        $requestItems = [
            $this->tableName => array_map(
                function ($key) use ($values) {
                    return [
                        'PutRequest' => [
                            'Item' => [
                                'id' => new AttributeValue(['S' => $key]),
                                'value' => new AttributeValue(['B' => $this->encode($values[$key])]),
                            ],
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
        $requestItems = [
            $this->tableName => array_map(
                function ($key) {
                    return [
                        'DeleteRequest' => [
                            'Key' => [
                                'id' => new AttributeValue(['S' => $key]),
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
        $result = $this->client->getItem(
            new GetItemInput(
                [
                    'TableName' => $this->tableName,
                    'Key' => [
                        'id' => new AttributeValue(['S' => $key]),
                    ],
                    'AttributesToGet' => [
                        'id',
                    ],
                ]
            )
        );

        return (bool) $result->getItem();
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
}
