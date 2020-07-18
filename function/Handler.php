<?php

declare(strict_types=1);

namespace App;

use Datashaman\Phial\ContextInterface;
use Exception;
use Negotiation\Negotiator;

final class Handler
{
    /**
     * @var Negotiator
     */
    private $negotiator;

    public function __construct(
        Negotiator $negotiator
    ) {
        $this->negotiator = $negotiator;
    }

    function __invoke(
        $event,
        ContextInterface $context
    ): array {
        $context->getLogger()->debug('Handle event', ['event' => $event]);

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

    private function negotiate(
        ContextInterface $context,
        string $acceptHeader,
        array $priorities
    ): array {
        $acceptsType = $this
            ->negotiator
            ->getBest(
                $acceptHeader,
                [
                    'text/html',
                    'application/json',
                ]
            )
            ->getType();

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
