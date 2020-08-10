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
    /**
     * @var array<string,array<string>>
     */
    protected $middleware = [
        'before' => [
            ExceptionMiddleware::class,
        ],

        'after' => [
            RouteMiddleware::class,
            FallbackMiddleware::class,
        ],
    ];

    public function getFactories()
    {
        return [
            RequestHandlerFactoryInterface::class => function (ContainerInterface $container) {
                return new RequestHandlerFactory(
                    array_merge(
                        $this->middleware['before'],
                        $container->get('app.middleware'),
                        $this->middleware['after'],
                    ),
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
