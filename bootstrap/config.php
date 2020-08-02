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
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

return [
    ClientInterface::class => DI\autowire(Client::class),
    ContextFactoryInterface::class => DI\create(ContextFactory::class),
    EventDispatcherInterface::class => function (ContainerInterface $container) {
        $provider = new ContainerListenerProvider($container);

        $provider->addService(StartEvent::class, StartEventListener::class);
        $provider->addService(RequestEvent::class, RequestEventListener::class);

        return new EventDispatcher($provider);
    },
    LoggerInterface::class => function () {
        $logger = new Logger('phial-handler');
        $formatter = new LineFormatter("%channel%.%level_name%: %message% %context% %extra%\n", null, false, true);
        $handler = new StreamHandler('php://stderr', Logger::DEBUG);
        $handler->setFormatter($formatter);
        $logger->pushHandler($handler);

        return $logger;
    },
    RequestFactoryInterface::class => DI\create(RequestFactory::class),
    Router::class => DI\create(Router::class)
        ->method('setStrategy', DI\get(StrategyInterface::class)),
    RequestHandlerInterface::class => DI\get(Router::class),
    RuntimeHandlerInterface::class => DI\autowire(RuntimeHandler::class),
    ServerRequestFactoryInterface::class => DI\create(ServerRequestFactory::class),
    StrategyInterface::class => DI\autowire(ApplicationStrategy::class)
        ->method('setContainer', DI\get(ContainerInterface::class)),
    StreamFactoryInterface::class => DI\create(StreamFactory::class),
];
