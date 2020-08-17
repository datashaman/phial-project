<?php

declare(strict_types=1);

return [
    'middleware' => [
        // This should be first to capture any exceptions
        // from middleware further down the pipeline.
        Datashaman\Phial\Http\Middleware\ExceptionMiddleware::class,

        // This should be last and handle all requests,
        // including not found responses.
        Datashaman\Phial\Http\Middleware\RouteMiddleware::class,
    ],
];
