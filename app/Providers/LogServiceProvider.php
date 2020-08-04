<?php

declare(strict_types=1);

namespace App\Providers;

use Interop\Container\ServiceProviderInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class LogServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            LineFormatter::class => fn(ContainerInterface $container) =>
                new LineFormatter(
                    $container->get('log.format'),
                    null,
                    false,
                    true
                ),
            LoggerInterface::class => fn(ContainerInterface $container) =>
                (new Logger($container->get('app.id')))
                    ->pushHandler($container->get(StreamHandler::class)),
            StreamHandler::class => function (ContainerInterface $container) {
                $handler = new StreamHandler($container->get('log.stream'), $container->get('log.level'));
                $handler->setFormatter($container->get(LineFormatter::class));
                $handler->pushProcessor($container->get(PsrLogMessageProcessor::class));

                return $handler;
            },
        ];
    }

    public function getExtensions()
    {
        return [];
    }
}
