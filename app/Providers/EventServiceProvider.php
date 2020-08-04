<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\RequestEventListener;
use App\Listeners\StartEventListener;
use App\Router;
use Circli\EventDispatcher\EventDispatcher;
use Circli\EventDispatcher\ListenerProvider\ContainerListenerProvider;
use Datashaman\Phial\Events\RequestEvent;
use Datashaman\Phial\Events\StartEvent;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;

class EventServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            ListenerProviderInterface::class => function (ContainerInterface $container) {
                $provider = new ContainerListenerProvider($container);

                $provider->addService(
                    StartEvent::class,
                    StartEventListener::class
                );

                $provider->addService(
                    RequestEvent::class,
                    RequestEventListener::class
                );

                return $provider;
            },
            EventDispatcherInterface::class => fn(ContainerInterface $container) =>
                $container->get(EventDispatcher::class),
        ];
    }

    public function getExtensions()
    {
        return [];
    }
}
