<?php

declare(strict_types=1);

namespace App\Providers;

use AsyncAws\CloudWatchLogs\CloudWatchLogsClient;
use AsyncAws\Monolog\CloudWatch\CloudWatchLogsHandler;
use Interop\Container\ServiceProviderInterface;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class LogServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            CloudWatchLogsHandler::class => function (ContainerInterface $container) {
                return new CloudWatchLogsHandler(
                    $container->get(CloudWatchLogsClient::class),
                    $container->get('log.handler')
                );
            },
            LoggerInterface::class => function (ContainerInterface $container) {
                $handler = $container->get(CloudWatchLogsHandler::class);

                return (new Logger($container->get('app.id')))
                    ->pushHandler($handler);
            },
        ];
    }

    public function getExtensions()
    {
        return [];
    }
}
