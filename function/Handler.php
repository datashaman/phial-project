<?php

declare(strict_types=1);

namespace App;

use Datashaman\Phial\ContextInterface;
use Exception;
use Negotiation\Negotiator;

final class Handler
{
    function __invoke(
        $event,
        ContextInterface $context,
        Negotiator $negotiator
    ): array {
        $logger = $context->getLogger();
        $logger->debug('Handle event', ['event' => $event]);

        if (isset($event['headers']['Accept'])) {
            return $this->negotiate(
                $context,
                $event['headers']['Accept'],
                [
                    'application/json' => [$this, 'json'],
                    'text/html' => [$this, 'html'],
                ],
                $negotiator
            );
        }

        return $this->json($context);
    }

    private function negotiate(
        ContextInterface $context,
        string $acceptHeader,
        array $priorities,
        Negotiator $negotiator
    ): array {
        $accepts = $negotiator->getBest(
            $acceptHeader,
            [
                'text/html',
                'application/json',
            ]
        );

        $acceptsType = $accepts->getType();

        $context->getLogger()->debug('Accept Negotation', ['acceptHeader' => $acceptHeader, 'acceptsType' => $acceptsType]);

        return call_user_func($priorities[$acceptsType], $context);
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
