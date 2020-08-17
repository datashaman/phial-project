<?php

declare(strict_types=1);

return [
    'debug' => is_true(env('APP_DEBUG', true)),
    'env' => env('APP_ENV', 'local'),

    'id' => 'phial-project',

    'providers' => [
        Datashaman\Phial\Providers\HandlerServiceProvider::class,
        Datashaman\Phial\Http\Providers\HttpServiceProvider::class,

        App\Providers\AwsServiceProvider::class,
        App\Providers\CacheServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\LogServiceProvider::class,
        App\Providers\TemplateServiceProvider::class,
        // App\Providers\TraceServiceProvider::class,
    ],
];
