<?php

declare(strict_types=1);

namespace App\Providers;

use App\Router;

use GuzzleHttp\Client;

use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;

use Psr\Http\Client\ClientInterface;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

use Psr\Http\Server\RequestHandlerInterface;

class HttpServiceProvider
{
    public function getFactories()
    {
        return [
            ClientInterface::class => function (ContainerInterface $container) {
                return new Client();
            },
            RequestFactoryInterface::class => function (ContainerInterface $container) {
                return new RequestFactory();
            },
            RequestHandlerInterface::class => function (ContainerInterface $container) {
                return $container->get(Router::class);
            },
            ServerRequestFactoryInterface::class => function (ContainerInterface $container) {
                return new ServerRequestFactory();
            },
            StreamFactoryInterface::class => function (ContainerInterface $container) {
                return new StreamFactory();
            },
        ];
    }
}
