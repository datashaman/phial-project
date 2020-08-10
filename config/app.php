<?php

declare(strict_types=1);

use function DI\env;

return [
    'app.debug' => env('APP_DEBUG', true),
    'app.env' => env('APP_ENV', 'local'),

    'app.id' => 'phial-project',

    'app.providers' => [
        App\Providers\EventServiceProvider::class,
        App\Providers\HandlerServiceProvider::class,
        App\Providers\HttpServiceProvider::class,
        App\Providers\LogServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\TemplateServiceProvider::class,
        // App\Providers\TraceServiceProvider::class,
    ],
];
