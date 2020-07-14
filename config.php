<?php

use GuzzleHttp\Client;

use Http\Factory\Guzzle\RequestFactory;
use Http\Factory\Guzzle\StreamFactory;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Logger;

use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

return [
    ClientInterface::class => function (ContainerInterface $container) {
        return new Client();
    },

    LoggerInterface::class => function (ContainerInterface $container) {
        $logger = new Logger('phial-handler');
        $formatter = new LineFormatter("%channel%.%level_name%: %message% %context% %extra%\n", null, false, true);
        $handler = new StreamHandler('php://stderr', Logger::DEBUG);
        $handler->setFormatter($formatter);
        $logger->pushHandler($handler);

        return $logger;
    },

    RequestFactoryInterface::class => DI\create(RequestFactory::class),
    StreamFactoryInterface::class => DI\create(StreamFactory::class),
];
