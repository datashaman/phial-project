<?php

declare(strict_types=1);

use App\Listeners\RequestEventListener;
use App\Listeners\StartEventListener;

use Circli\EventDispatcher\EventDispatcher;
use Circli\EventDispatcher\ListenerProvider\ContainerListenerProvider;

use Datashaman\Phial\Events\RequestEvent;
use Datashaman\Phial\Events\StartEvent;

use Psr\Container\ContainerInterface;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

return [
    EventDispatcherInterface::class => DI\create(EventDispatcher::class)
        ->constructor(DI\get(ListenerProviderInterface::class)),
    ListenerProviderInterface::class => DI\create(ContainerListenerProvider::class)
        ->constructor(DI\get(ContainerInterface::class))
        ->method('addService', StartEvent::class, StartEventListener::class)
        ->method('addService', RequestEvent::class, RequestEventListener::class),
];
