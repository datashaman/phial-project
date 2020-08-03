<?php

declare(strict_types=1);

use App\Providers\EventServiceProvider;
use App\Providers\HttpServiceProvider;
use App\Providers\LogServiceProvider;

use function DI\get;

return [
    'app.id' => 'phial-project',

    'app.providers' => [
        get(EventServiceProvider::class),
        get(HttpServiceProvider::class),
        get(LogServiceProvider::class),
        get(HandlerServiceProvider::class),
    ],
];
