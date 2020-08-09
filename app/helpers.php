<?php

if (!function_exists('abort')) {
    /**
     * @param array<string,string|array<string,string>> $headers
     */
    function abort(
        int $code,
        string $message = '',
        array $headers = [],
        ?Throwable $previous = null
    ): void {
        throw App\Exceptions\HttpException::create(
            $message,
            $code,
            $previous,
            $headers
        );
    }
}

if (!function_exists('is_true')) {
    /**
     * @param mixed $value
     */
    function is_true($value): bool
    {
        $value = is_string($value)
            ? mb_strtolower($value)
            : $value;

        $checks = [
            null,
            false,
            'false',
            'off',
            'no',
            '0',
            '',
            0,
            0.0,
        ];

        return !in_array(
            $value,
            $checks,
            true
        );
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
