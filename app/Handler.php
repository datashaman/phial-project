<?php

declare(strict_types=1);

namespace App;

use Datashaman\Phial\ContextInterface;
use Exception;

final class Handler
{
    function __invoke(
        $event,
        ContextInterface $context
    ): array {
        $context->getLogger()->debug('Handle event', ['event' => $event, 'env' => getenv()]);

        if (isset($event['headers']['Accept'])) {
            return $this->negotiate(
                $context,
                $event['headers']['Accept'],
                [
                    'application/json' => [$this, 'json'],
                    'text/html' => [$this, 'html'],
                ]
            );
        }

        return $this->json($context);
    }
    private function html(ContextInterface $context)
    {
        return [
            'statusCode' => 200,
            'body' => 'hello world',
            'headers' => [
                'Content-Type' => 'text/html',
            ],
        ];
    }

    private function json(ContextInterface $context)
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
