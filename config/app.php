<?php

declare(strict_types=1);

use function DI\env;

return [
    'app.debug' => env('APP_DEBUG', true),

    'app.id' => 'phial-project',

    'app.middleware' => [
        App\Middleware\ExceptionMiddleware::class,
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
