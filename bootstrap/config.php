<?php

use App\Listeners\RequestEventListener;
use App\Listeners\StartEventListener;
use App\Router as AppRouter;
use App\Strategy\ApplicationStrategy;

use Circli\EventDispatcher\EventDispatcher;
use Circli\EventDispatcher\ListenerProvider\ContainerListenerProvider;

use Datashaman\Phial\ContextFactory;
use Datashaman\Phial\ContextFactoryInterface;
use Datashaman\Phial\ContextInterface;
use Datashaman\Phial\Events\RequestEvent;
use Datashaman\Phial\Events\StartEvent;

use Datashaman\Phial\RuntimeHandler;
use Datashaman\Phial\RuntimeHandlerInterface;

use GuzzleHttp\Client;

use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;

use League\Route\RouteCollectionInterface;
use League\Route\Strategy\StrategyInterface;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Logger;

use DI\Container;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

return [
    'app.id' => 'phial-project',

    'log.format' => "%channel%.%level_name%: %message% %context% %extra%\n",
    'log.level' => Logger::DEBUG,
    'log.stream' => 'php://stderr',

    AppRouter::class => DI\create()
        ->method('setStrategy', DI\get(StrategyInterface::class)),
    RequestHandlerInterface::class => DI\get(AppRouter::class),
    RouteCollectionInterface::class => DI\get(AppRouter::class),
    StrategyInterface::class => DI\autowire(ApplicationStrategy::class)
        ->method('setContainer', DI\get(ContainerInterface::class)),

    EventDispatcherInterface::class => DI\autowire(EventDispatcher::class),
    ListenerProviderInterface::class => DI\autowire(ContainerListenerProvider::class)
        ->method('addService', StartEvent::class, StartEventListener::class)
        ->method('addService', RequestEvent::class, RequestEventListener::class),

    LineFormatter::class => DI\create(LineFormatter::class)
        ->constructor(DI\get('log.format'), null, false, true),
    LoggerInterface::class => DI\create(Logger::class)
        ->constructor(DI\get('app.id'))
        ->method('pushHandler', DI\get(StreamHandler::class)),
    StreamHandler::class => DI\create()
        ->constructor(DI\get('log.stream'), DI\get('log.level'))
        ->method('setFormatter', DI\get(LineFormatter::class)),

    ClientInterface::class => DI\autowire(Client::class),
    ContextFactoryInterface::class => DI\create(ContextFactory::class),
    RuntimeHandlerInterface::class => DI\autowire(RuntimeHandler::class),

    RequestFactoryInterface::class => DI\create(RequestFactory::class),
    ServerRequestFactoryInterface::class => DI\create(ServerRequestFactory::class),
    StreamFactoryInterface::class => DI\create(StreamFactory::class),
];
