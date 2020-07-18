<?php

declare(strict_types=1);

namespace App;

use Datashaman\Phial\ContextInterface;
use Exception;

final class HelloHandler extends AbstractHandler
{
    /**
     * @param array<string|array> $event
     *
     * @return array<string, array<string, string>|int|string>
     */
    public function html($event, ContextInterface $context): array
    {
        return [
            'statusCode' => 200,
            'body' => 'hello world',
            'headers' => [
                'Content-Type' => 'text/html',
            ],
        ];
    }

    /**
     * @param array<string|array> $event
     *
     * @return array<string, array<string, string>|int|string>
     */
    public function json($event, ContextInterface $context): array
    {
        return [
            'statusCode' => 200,
            'body' => json_encode(
                [
                    'message' => 'hello world',
                    'functionName' => $context->getFunctionName(),
                ],
                JSON_THROW_ON_ERROR
            ),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ];
    }
}
