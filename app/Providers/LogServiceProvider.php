<?php

declare(strict_types=1);

namespace App\Providers;

use Interop\Container\ServiceProviderInterface;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Logger;

use Psr\Log\LoggerInterface;

class LogServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            LineFormatter::class => function (ContainerInterface $container) {
                return new LineFormatter(
                    $container->get('logger.format'),
                    null,
                    false,
                    true
                );
            },
            LoggerInterface::class => function (ContainerInterface $container) {
                return (new Logger($container->get('app.id')))
                    ->pushHandler($container->get(StreamHandler::class));
            },
            StreamHandler::class => function (ContainerInterface $container) {
                return (new StreamHandler($container->get('log.stream'), $container->get('log.level')))
                    ->pushProcessor($container->get(PsrLogMessageProcessor::class))
                    ->setFormatter($container->get(LineFormatter::class));
            },
        ];
    }
}
