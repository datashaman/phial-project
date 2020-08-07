<?php

declare(strict_types=1);

namespace App\Providers;

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
                    function (RouteCollector $r) {
                        require base_dir('routes/web.php');
                    },
                    [
                        'cacheDisabled' => $container->get('app.debug'),
                        'cacheFile' => base_dir('cache/routes.php'),
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
