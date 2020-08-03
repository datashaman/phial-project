<?php

declare(strict_types=1);

namespace App\ServiceProviders;

use App\Listeners\RequestEventListener;
use App\Listeners\StartEventListener;

use Circli\EventDispatcher\EventDispatcher;
use Circli\EventDispatcher\ListenerProvider\ContainerListenerProvider;

use Datashaman\Phial\Events\RequestEvent;
use Datashaman\Phial\Events\StartEvent;

use Interop\Container\ServiceProviderInterface;

use Psr\Container\ContainerInterface;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class EventServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            ContainerListenerProvider::class => function (ContainerInterface $container) {
                $provider = new ContainerListenerProvider($container);
                $provider->addService(StartEvent::class, StartEventListener::class);
                $provider->addService(RequestEvent::class, RequestEventListener::class);

                return $provider;
            },
            EventDispatcherInterface::class => function (ContainerInterface $container) {
                return new EventDispatcher(
                    $container->get(ContainerListenerProvider::class)
                );
            },
        ];
    }
}
