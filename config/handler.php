<?php

declare(strict_types=1);

use Datashaman\Phial\ContextFactory;
use Datashaman\Phial\ContextFactoryInterface;

use Datashaman\Phial\RuntimeHandler;
use Datashaman\Phial\RuntimeHandlerInterface;

use Invoker\InvokerInterface;

use Psr\Http\Client\ClientInterface;

use Psr\EventDispatcher\EventDispatcherInterface;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

use Psr\Log\LoggerInterface;

return [
    ContextFactoryInterface::class => DI\create(ContextFactory::class),
    RuntimeHandlerInterface::class => DI\create(RuntimeHandler::class)
        ->constructor(
            DI\get(ClientInterface::class),
            DI\get(RequestFactoryInterface::class),
            DI\get(StreamFactoryInterface::class),
            DI\get(InvokerInterface::class),
            DI\get(LoggerInterface::class),
            DI\get(ContextFactoryInterface::class),
            DI\get(EventDispatcherInterface::class)
        ),
];
