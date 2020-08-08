<?php

declare(strict_types=1);

namespace App\Providers;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;

use function FastRoute\simpleDispatcher;

class RouteServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            Dispatcher::class => fn(ContainerInterface $container) => simpleDispatcher(
                function (RouteCollector $r) {
                    require base_dir('routes/web.php');
                }
            ),
        ];
    }

    public function getExtensions()
    {
        return [];
    }
}
