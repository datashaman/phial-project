<?php

declare(strict_types=1);

use function DI\create;
use function DI\env;
use function DI\get;

return [
    'app.debug' => env('APP_DEBUG', true),
    'app.env' => env('APP_ENV', 'local'),

    'app.id' => 'phial-project',

    'app.middleware' => [
        get(App\Http\Middleware\ExceptionMiddleware::class),
        create(Middlewares\JsonPayload::class)->method('associative', false),
        get(App\Http\Middleware\RouteMiddleware::class),
    ],

    'app.providers' => [
        App\Providers\EventServiceProvider::class,
        App\Providers\HandlerServiceProvider::class,
        App\Providers\HttpServiceProvider::class,
        App\Providers\LogServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ],
];
