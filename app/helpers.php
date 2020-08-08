<?php

if (!function_exists('abort')) {
    function abort(
        int $statusCode,
        string $reasonPhrase = '',
        array $headers = []
    ): void {
        throw App\Exceptions\HttpException::create($statusCode, $reasonPhrase, $headers);
    }
}

if (!function_exists('base_dir')) {
    define('BASE_DIR', realpath(__DIR__ . '/../'));

    function base_dir(string $path = ''): string
    {
        if ($path && $path[0] === '/') {
            $path = substr($path, 1);
        }

        return BASE_DIR . ($path ? "/$path" : '');
    }
}
