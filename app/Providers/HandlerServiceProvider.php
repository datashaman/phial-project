<?php

declare(strict_types=1);

namespace App\Providers;

use Datashaman\Phial\ContextFactory;
use Datashaman\Phial\ContextFactoryInterface;
use Datashaman\Phial\RequestHandlerAdapter;
use Datashaman\Phial\RuntimeHandler;
use Datashaman\Phial\RuntimeHandlerInterface;
use Interop\Container\ServiceProviderInterface;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class HandlerServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            ContextFactoryInterface::class => fn(ContainerInterface $container) =>
                $container->get(ContextFactory::class),
            RuntimeHandlerInterface::class => fn(ContainerInterface $container) =>
                $container->get(RuntimeHandler::class),
        ];
    }

    public function getExtensions()
    {
        return [];
    }
}
