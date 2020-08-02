<?php

use App\Listeners\RequestEventListener;
use App\Listeners\StartEventListener;
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

use League\Route\Router;
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

    ClientInterface::class => DI\autowire(Client::class),
    ContextFactoryInterface::class => DI\create(ContextFactory::class),
    EventDispatcherInterface::class => DI\autowire(EventDispatcher::class),
    LineFormatter::class => DI\create(LineFormatter::class)
        ->constructor(DI\get('log.format'), null, false, true),
    ListenerProviderInterface::class => DI\autowire()
        ->method('addService', StartEvent::class, StartEventListener::class)
        ->method('addService', RequestEvent::class, RequestEventListener::class),
    LoggerInterface::class => DI\create(Logger::class)
        ->constructor(DI\get('app.id'))
        ->method('pushHandler', DI\get(StreamHandler::class)),
    RequestFactoryInterface::class => DI\create(RequestFactory::class),
    RequestHandlerInterface::class => DI\get(Router::class),
    Router::class => DI\create(Router::class)
        ->method('setStrategy' , DI\get(StrategyInterface::class)),
    RuntimeHandlerInterface::class => DI\autowire(RuntimeHandler::class),
    ServerRequestFactoryInterface::class => DI\create(ServerRequestFactory::class),
    StrategyInterface::class => DI\autowire(ApplicationStrategy::class)
        ->method('setContainer', DI\get(ContainerInterface::class)),
    StreamFactoryInterface::class => DI\create(StreamFactory::class),
    StreamHandler::class => DI\create()
        ->constructor(DI\get('log.stream'), DI\get('log.level'))
        ->method('setFormatter', DI\get(LineFormatter::class)),
];
