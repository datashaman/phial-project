<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Narrowspark\HttpStatus\HttpStatus;
use Throwable;

class HttpException extends Exception implements StatusCodeInterface
{
    private array $headers = [];

    public static function create(
        string $message = '',
        int $code = 500,
        Throwable $previous = null,
        array $headers = []
    ): self {
        if (!$message) {
            $message = HttpStatus::getReasonPhrase($code);
        }

        $exception = new self($message, $code, $previous);

        return $exception->withHeaders($headers);
    }

    public function withHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function toJsonResponse(): JsonResponse
    {
        $payload = [
            'message' => $this->getMessage(),
            'trace' => $this->getTrace(),
        ];

        if ($this->getPrevious()) {
            $payload['previous'] = $this->getPrevious();
        }

        return new JsonResponse(
            $payload,
            $this->getCode(),
            $this->headers
        );
    }
}
