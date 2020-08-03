<?php

declare(strict_types=1);

namespace App\Providers;

use Datashaman\Phial\ContextFactory;
use Datashaman\Phial\ContextFactoryInterface;

use Datashaman\Phial\RuntimeHandler;
use Datashaman\Phial\RuntimeHandlerInterface;

use Invoker\InvokerInterface;

use Psr\Container\ContainerInterface;

use Psr\EventDispatcher\EventDispatcherInterface;

use Psr\Http\Client\ClientInterface;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

use Psr\Log\LoggerInterface;

class HandlerServiceProvider
{
    public function getFactories()
    {
        return [
            ContextFactoryInterface::class => function (ContainerInterface $container) {
                return new ContextFactory();
            },
            RuntimeHandlerInterface::class => function (ContainerInterface $container) {
                return new RuntimeHandler(
                    $container->get(ClientInterface::class),
                    $container->get(RequestFactoryInterface::class),
                    $container->get(StreamFactoryInterface::class),
                    $container->get(InvokerInterface::class),
                    $container->get(LoggerInterface::class),
                    $container->get(ContextFactoryInterface::class),
                    $container->get(EventDispatcherInterface::class)
                );
            },
        ];
    }
}
