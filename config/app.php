<?php

declare(strict_types=1);

return [
    'app.debug' => false,

    'app.id' => 'phial-project',

    'app.middleware' => [
        Franzl\Middleware\Whoops\WhoopsMiddleware::class,
        App\Middleware\TestMiddleware::class,
        App\Middleware\RouteMiddleware::class,
    ],

    'app.providers' => [
        App\Providers\EventServiceProvider::class,
        App\Providers\HandlerServiceProvider::class,
        App\Providers\HttpServiceProvider::class,
        App\Providers\LogServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ],
];
