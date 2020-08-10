<?php

declare(strict_types=1);

namespace App\Caches;

use AsyncAws\DynamoDb\DynamoDbClient;
use AsyncAws\DynamoDb\Input\CreateTableInput;
use AsyncAws\DynamoDb\Input\DescribeTableInput;
use AsyncAws\DynamoDb\Input\GetItemInput;
use AsyncAws\DynamoDb\Input\PutItemInput;
use AsyncAws\DynamoDb\ValueObject\AttributeDefinition;
use AsyncAws\DynamoDb\ValueObject\AttributeValue;
use AsyncAws\DynamoDb\ValueObject\KeySchemaElement;
use AsyncAws\DynamoDb\ValueObject\ProvisionedThroughput;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

class DynamoDbCache implements CacheInterface
{
    private string $tableName;
    private DynamoDbClient $client;
    private LoggerInterface $logger;

    public function __construct(
        string $tableName,
        DynamoDbClient $client,
        LoggerInterface $logger
    ) {
        $this->tableName = $tableName;
        $this->client = $client;
        $this->logger = $logger;

        // $this->createTable();
    }

    public function get($key, $default = null)
    {
        $result = $this->client->getItem(
            new GetItemInput(
                [
                    'TableName' => $this->tableName,
                    'ConsistentRead' => true,
                    'Key' => [
                        'Key' => new AttributeValue(['S' => $key]),
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
                    $item['Value']->getB()
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
                        'Key' => new AttributeValue(['S' => $key]),
                        'Value' => new AttributeValue(['B' => gzcompress(serialize($value))]),
                    ]
                ]
            )
        );
    }

    public function delete($key)
    {
    }

    public function clear()
    {
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
    }

    private function createTable()
    {
        $this->client->createTable(
            new CreateTableInput(
                [
                    'TableName' => $this->tableName,
                    'AttributeDefinitions' => [
                        new AttributeDefinition(['AttributeName' => 'Key', 'AttributeType' => 'S']),
                    ],
                    'KeySchema' => [
                        new KeySchemaElement(['AttributeName' => 'Key', 'KeyType' => 'HASH']),
                    ],
                    'ProvisionedThroughput' => new ProvisionedThroughput(
                        [
                            'ReadCapacityUnits' => 5,
                            'WriteCapacityUnits' => 5,
                        ]
                    ),
                ]
            )
        );

        $this->client->tableExists(
            new DescribeTableInput(
                [
                    'TableName' => $this->tableName,
                ]
            )
        )->wait();
    }
}
