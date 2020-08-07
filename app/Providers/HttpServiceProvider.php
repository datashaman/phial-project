<?php

declare(strict_types=1);

namespace App\Providers;

use App\RequestHandler;
use GuzzleHttp\Client;
use Interop\Container\ServiceProviderInterface;
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

class HttpServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            ClientInterface::class => fn(ContainerInterface $container) =>
                $container->get(Client::class),
            RequestFactoryInterface::class => fn(ContainerInterface $container) =>
                $container->get(RequestFactory::class),
            RequestHandlerInterface::class => fn(ContainerInterface $container) =>
                $container->get(RequestHandler::class),
            ServerRequestFactoryInterface::class => fn(ContainerInterface $container) =>
                $container->get(ServerRequestFactory::class),
            StreamFactoryInterface::class => fn(ContainerInterface $container) =>
                $container->get(StreamFactory::class),
        ];
    }

    public function getExtensions()
    {
        return [];
    }
}
