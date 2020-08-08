<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Fig\Http\Message\StatusCodeInterface;

class HttpException extends Exception implements StatusCodeInterface
{
    private static array $classMap = [
        self::STATUS_NOT_FOUND => NotFoundException::class,
    ];

    private array $headers = [];

    public static function create(
        int $statusCode,
        string $reasonPhrase = '',
        array $headers = []
    ): self {
        $exception = ($className = self::$classMap[$statusCode])
            ? new $className($reasonPhrase)
            : new self($reasonPhrase, $statusCode);

        return $exception->withHeaders($headers);
    }

    public function withHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }
}
