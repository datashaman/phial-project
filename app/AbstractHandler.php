<?php

declare(strict_types=1);

namespace App;

use Datashaman\Phial\ContextInterface;
use Invoker\InvokerInterface;
use Negotiation\Negotiator;

abstract class AbstractHandler
{
    /**
     * @var Negotiator
     */
    private $negotiator;

    /**
     * @var InvokerInterface
     */
    private $invoker;

    public function __construct(
        Negotiator $negotiator,
        InvokerInterface $invoker
    ) {
        $this->negotiator = $negotiator;
        $this->invoker = $invoker;
    }

    /**
     * @param array<string|array> $event
     * @param array<string, array<int, callable|string>> $priorities
     *
     * @return array<string|array>
     */
    protected function negotiate(
        array $event,
        ContextInterface $context,
        array $priorities,
        string $default
    ): array {
        $accept = $this->accept($event, $default);
        $context->getLogger()->debug('Content Negotation', ['accept' => $accept]);

        /** @var callable $callable */
        $callable = $priorities[$accept];

        return $this->invoker->call(
            $callable,
            [
                'event' => $event,
                'context' => $context,
            ]
        );
    }

    /**
     * @param array<string|array> $event
     */
    private function accept(
        array $event,
        string $default
    ): string {
        $headers = $event['headers'] ?? [];
        $header = $headers['Accept'] ?? $default;

        $best = $this
            ->negotiator
            ->getBest(
                $header,
                [
                    'text/html',
                    'application/json',
                ]
            );

        if (!$best) {
            return $default;
        }

        return $best->getType();
    }
}
