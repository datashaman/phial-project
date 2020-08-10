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
    /**
     * @var array<string,string|array<string,string>>
     */
    private array $headers = [];

    private bool $debug;

    /**
     * @param array<string,string|array<string,string>> $headers
     */
    public static function create(
        string $message = '',
        int $code = 500,
        Throwable $previous = null,
        array $headers = []
    ): self {
        if (!$message) {
            $message = HttpStatus::getReasonPhrase($code);
        }

        $exception = new HttpException($message, $code, $previous);

        return $exception->withHeaders($headers);
    }

    /**
     * @param array<string,string|array<string,string>> $headers
     */
    public function withHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function debug(bool $debug): self
    {
        $this->debug = $debug;

        return $this;
    }

    public function toJsonResponse(): JsonResponse
    {
        $payload = [
            'message' => $this->getMessage(),
        ];

        $code = (int) $this->getCode();

        if ($this->debug) {
            if ($code === 500) {
                $payload['trace'] = $this->getTrace();
            }

            if ($previous = $this->getPrevious()) {
                $payload['previous'] = $previous;
            }
        }

        return new JsonResponse(
            $payload,
            $code,
            $this->headers
        );
    }
}
