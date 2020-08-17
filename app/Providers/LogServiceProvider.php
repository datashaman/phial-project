<?php

declare(strict_types=1);

namespace App\Providers;

use Datashaman\Phial\ConfigInterface;
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
                    $container->get(ConfigInterface::class)->get('log.format'),
                    null,
                    false,
                    true
                ),
            Logger::class => fn(ContainerInterface $container) =>
                new Logger($container->get(ConfigInterface::class)->get('app.id')),
            LoggerInterface::class => fn(ContainerInterface $container): LoggerInterface =>
                $container->get(Logger::class),
            StreamHandler::class => function (ContainerInterface $container) {
                $config = $container->get(ConfigInterface::class);
                $handler = new StreamHandler($config->get('log.stream'), $config->get('log.level'));
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
