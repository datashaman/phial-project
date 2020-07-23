<?php

use Datashaman\Phial\ContextFactory;
use Datashaman\Phial\ContextFactoryInterface;

use Datashaman\Phial\RuntimeHandler;
use Datashaman\Phial\RuntimeHandlerInterface;

use GuzzleHttp\Client;

use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Logger;

use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

return [
    ClientInterface::class => DI\autowire(Client::class),
    ContextFactoryInterface::class => DI\create(ContextFactory::class),

    LoggerInterface::class => function (ContainerInterface $container) {
        $logger = new Logger('phial-handler');
        $formatter = new LineFormatter("%channel%.%level_name%: %message% %context% %extra%\n", null, false, true);
        $handler = new StreamHandler('php://stderr', Logger::DEBUG);
        $handler->setFormatter($formatter);
        $logger->pushHandler($handler);

        return $logger;
    },

    RequestFactoryInterface::class => DI\create(RequestFactory::class),
    RuntimeHandlerInterface::class => DI\autowire(RuntimeHandler::class),
    ServerRequestFactoryInterface::class => DI\create(ServerRequestFactory::class),
    StreamFactoryInterface::class => DI\create(StreamFactory::class),
];
