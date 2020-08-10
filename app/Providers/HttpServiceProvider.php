<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Middleware\ExceptionMiddleware;
use App\Http\Middleware\RouteMiddleware;
use App\Http\Middleware\FallbackMiddleware;
use App\Http\RequestHandlers\RequestHandlerFactory;
use Datashaman\Phial\Http\RequestHandlerFactoryInterface;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class HttpServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            ExceptionMiddleware::class => fn(ContainerInterface $container)  =>
                new ExceptionMiddleware(
                    $container->get(LoggerInterface::class),
                    is_true($container->get('app.debug'))
                ),
            RequestHandlerFactoryInterface::class => fn(ContainerInterface $container) =>
                new RequestHandlerFactory(
                    $container->get('http.middleware'),
                    $container
                ),
        ];
    }

    public function getExtensions()
    {
        return [];
    }
}
