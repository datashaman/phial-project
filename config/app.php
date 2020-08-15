<?php

declare(strict_types=1);

use function DI\env;

return [
    'appDebug' => env('APP_DEBUG', true),
    'appEnv' => env('APP_ENV', 'local'),

    'appId' => 'phial-project',

    'appProviders' => [
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
