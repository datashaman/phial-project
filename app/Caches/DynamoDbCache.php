<?php

declare(strict_types=1);

namespace App\Caches;

use AsyncAws\Core\AwsClientFactory;
use AsyncAws\DynamoDb\DynamoDbClient;
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

        $item = $result->getItem();

        if (!$item) {
            throw new InvalidArgumentException(
                sprintf("'%s' is not a valid cache key.", $key)
            );
        }

        return unserialize(
            gzuncompress(
                base64_decode(
                    $item['value']->getB()
                )
            )
        );
    }

    public function set($key, $value, $ttl = null)
    {
        $this->client->putItem(
            new PutItemInput(
                [
                    'TableName' => $this->tableName,
                    'Item' => [
                        'id' => new AttributeValue(['S' => $key]),
                        'value' => new AttributeValue(['B' => gzcompress(serialize($value))]),
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

        $items = [];

        foreach ($result->getItems() as $item) {
            $items[] = [
                'DeleteRequest' => [
                    'Key' => [
                        'id' => $item['id'],
                    ],
                ]
            ];
        }

        if ($items) {
            $result = $this->client->batchWriteItem(
                new BatchWriteItemInput(
                    [
                        'RequestItems' => [
                            $this->tableName => $items,
                        ],
                    ]
                ),
            );
        }

        return true;
    }

    public function getMultiple($keys, $default = null)
    {
    }

    public function setMultiple($values, $ttl = null)
    {
    }

    public function deleteMultiple($keys)
    {
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
}
