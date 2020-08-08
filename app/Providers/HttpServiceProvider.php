<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\RequestHandlers\FallbackRequestHandler;
use App\Http\RequestHandlers\RequestHandlerFactory;
use Datashaman\Phial\RequestHandlerFactoryInterface;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;

class HttpServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            RequestHandlerFactoryInterface::class => fn(ContainerInterface $container) =>
                new RequestHandlerFactory(
                    $container->get('app.middleware'),
                    $container->get(FallbackRequestHandler::class)
                ),
        ];
    }

    public function getExtensions()
    {
        return [];
    }
}
