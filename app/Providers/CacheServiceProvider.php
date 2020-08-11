<?php

declare(strict_types=1);

namespace App\Providers;

use App\Caches\DynamoDbCache;
use AsyncAws\Core\AwsClientFactory;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

class CacheServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            CacheInterface::class => fn(ContainerInterface $container) =>
                new DynamoDbCache(
                    $container->get('cache.dynamodb.tableName'),
                    $container->get(LoggerInterface::class),
                    $container->get(AwsClientFactory::class)
                ),
        ];
    }

    public function getExtensions()
    {
        return [];
    }
}
