<?php

declare(strict_types=1);

return [
    'app.id' => 'phial-project',

    'app.providers' => [
        App\Providers\EventServiceProvider::class,
        App\Providers\HandlerServiceProvider::class,
        App\Providers\HttpServiceProvider::class,
        App\Providers\LogServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ],
];
