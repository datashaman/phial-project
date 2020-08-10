<?php

declare(strict_types=1);

use function DI\env;
use function DI\get;

return [
    'app.debug' => env('APP_DEBUG', true),
    'app.env' => env('APP_ENV', 'local'),

    'app.id' => 'phial-project',

    'app.middleware' => [
        get(App\Http\Middleware\ExceptionMiddleware::class),
        get(Middlewares\JsonPayload::class),
        get(App\Http\Middleware\RouteMiddleware::class),
    ],

    'app.providers' => [
        App\Providers\AwsServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\HandlerServiceProvider::class,
        App\Providers\HttpServiceProvider::class,
        App\Providers\LogServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\TraceServiceProvider::class,
    ],
];
