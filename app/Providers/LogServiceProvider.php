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
            CloudWatchLogsHandler::class => fn(ContainerInterface $container) =>
                new CloudWatchLogsHandler(
                    $container->get(CloudWatchLogsClient::class),
                    $container->get('log.cloudwatch.handler')
                ),
            Logger::class => fn(ContainerInterface $container) =>
                new Logger($container->get('app.id')),
            LoggerInterface::class => fn(ContainerInterface $container) =>
                $container->get(Logger::class),
        ];
    }

    public function getExtensions()
    {
        return [
            Logger::class => fn(ContainerInterface $container, Logger $logger) =>
                $logger->pushHandler($container->get(CloudWatchLogsHandler::class)),
        ];
    }
}
