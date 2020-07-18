<?php

declare(strict_types=1);

namespace App;

use Datashaman\Phial\ContextInterface;
use Exception;

final class Handler extends AbstractHandler
{
    /**
     * @param array<string|array> $event
     *
     * @return array<string|array>
     */
    function __invoke(
        $event,
        ContextInterface $context
    ): array {
        $context->getLogger()->debug('Handle event', ['event' => $event, 'env' => getenv()]);

        return $this->negotiate(
            $event,
            $context,
            [
                'application/json' => [$this, 'json'],
                'text/html' => [$this, 'html'],
            ],
            'application/json'
        );
    }

    /**
     * @param array<string|array> $event
     *
     * @return array<string, array<string, string>|int|string>
     */
    protected function html($event, ContextInterface $context): array
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
    protected function json($event, ContextInterface $context): array
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
