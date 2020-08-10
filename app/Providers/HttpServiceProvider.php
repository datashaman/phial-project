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

class HttpServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            RequestHandlerFactoryInterface::class => function (ContainerInterface $container) {
                return new RequestHandlerFactory(
                    $container->get('http.middleware'),
                    $container
                );
            }
        ];
    }

    public function getExtensions()
    {
        return [];
    }
}
