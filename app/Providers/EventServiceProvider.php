<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\SetRequestAndContext;
use Circli\EventDispatcher\EventDispatcher;
use Circli\EventDispatcher\ListenerProvider\ContainerListenerProvider;
use Datashaman\Phial\Events\RequestEvent;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class EventServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            ListenerProviderInterface::class => fn(ContainerInterface $container) =>
                $container->get(ContainerListenerProvider::class),
            EventDispatcherInterface::class => fn(ContainerInterface $container) =>
                $container->get(EventDispatcher::class),
        ];
    }

    public function getExtensions()
    {
        return [
            ListenerProviderInterface::class => function (ContainerInterface $container, $provider) {
                $provider->addService(RequestEvent::class, SetRequestAndContext::class);

                return $provider;
            },
        ];
    }
}
