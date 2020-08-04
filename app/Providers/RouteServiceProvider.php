<?php

declare(strict_types=1);

namespace App\Providers;

use App\Controllers\HomeController;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;

use function FastRoute\cachedDispatcher;

class RouteServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            Dispatcher::class => function (ContainerInterface $container) {
                return cachedDispatcher(
                    function(RouteCollector $r) {
                        $r->addRoute('GET', '/', HelloController::class . '@hello');
                    },
                    [
                        'cacheFile' => 'cache/route',
                    ]
                );
            },
        ];
    }

    public function getExtensions()
    {
        return [];
    }
}
