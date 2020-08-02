<?php

declare(strict_types=1);

use App\Router;

use GuzzleHttp\Client;

use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

return [
    ClientInterface::class => DI\create(Client::class),
    RequestFactoryInterface::class => DI\create(RequestFactory::class),
    RequestHandlerInterface::class => DI\get(Router::class),
    Router::class => DI\create(Router::class),
    ServerRequestFactoryInterface::class => DI\create(ServerRequestFactory::class),
    StreamFactoryInterface::class => DI\create(StreamFactory::class),
];
