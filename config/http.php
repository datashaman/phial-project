<?php

declare(strict_types=1);

return [
    'http.middleware' => [
        // This should be first to capture any exceptions
        // from middleware further down the pipeline.
        App\Http\Middleware\ExceptionMiddleware::class,

        // This should be last and handle all requests,
        // including not found responses.
        App\Http\Middleware\RouteMiddleware::class,
    ],
];
