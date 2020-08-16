<?php

declare(strict_types=1);

namespace App\Providers;

use Interop\Container\ServiceProviderInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class LogServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            LineFormatter::class => fn(ContainerInterface $container) =>
                new LineFormatter(
                    $container->get('logFormat'),
                    null,
                    false,
                    true
                ),
            Logger::class => fn(ContainerInterface $container) =>
                new Logger($container->get('appId')),
            LoggerInterface::class => fn(ContainerInterface $container): LoggerInterface =>
                $container->get(Logger::class),
            StreamHandler::class => function (ContainerInterface $container) {
                $handler = new StreamHandler($container->get('logStream'), $container->get('logLevel'));
                $handler->setFormatter($container->get(LineFormatter::class));
                $handler->pushProcessor($container->get(PsrLogMessageProcessor::class));

                return $handler;
            },
        ];
    }

    public function getExtensions()
    {
        return [
            Logger::class => fn(ContainerInterface $container, Logger $logger) =>
                $logger->pushHandler($container->get(StreamHandler::class)),
        ];
    }
}
