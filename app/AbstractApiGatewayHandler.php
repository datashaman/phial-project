<?php

declare(strict_types=1);

namespace App;

use Datashaman\Phial\ContextInterface;
use Invoker\InvokerInterface;
use Negotiation\Negotiator;
use Psr\Http\Message\ServerRequestFactoryInterface;

abstract class AbstractApiGatewayHandler extends AbstractHandler
{
    /**
     * @var ServerRequestFactoryInterface
     */
    private $serverRequestFactory;

    public function __construct(
        Negotiator $negotiator,
        InvokerInterface $invoker,
        ServerRequestFactoryInterface $serverRequestFactory
    ) {
        parent::__construct($negotiator, $invoker);

        $this->serverRequestFactory = $serverRequestFactory;
    }

    /**
     * @param array<string|array> $event
     */
    function __invoke(
        array $event,
        ContextInterface $context
    ): string {
        $context->getLogger()->debug('Handle event', ['event' => $event, 'context' => $context->toArray()]);

        return json_encode(
            $this->negotiate(
                $event,
                $context,
                [
                    'application/json' => [$this, 'json'],
                    'text/html' => [$this, 'html'],
                ],
                'application/json'
            ), JSON_THROW_ON_ERROR
        );
    }

    private function createServerRequest(
        array $event,
        ContextInterface $context
    ): ServerRequestInterface {
        $request = $this->serverRequestFactory
            ->createServerRequest(
                $event['httpMethod'],
                $event['path'],
                $this->generateServerParams($event, $context)
            );

        foreach ($event['headers'] as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        foreach ($event['multiValueHeaders'] as $name => $values) {
            foreach ($values as $index => $value) {
                $request = $index
                    ? $request->withAddedHeader($name, $value)
                    : $request->withHeader($name, $value);
            }
        }

        $queryParams = [];

        if (isset($event['queryStringParameters'])) {
            foreach ($event['queryStringParameters'] as $name => $value) {
                if (!$this->endsWith($name, '[]')) {
                    $queryParams[$name] = $value;
                }
            }
        }

        if (isset($event['multiValueQueryStringParameters'])) {
            foreach ($event['multiValueQueryStringParameters'] as $name => $value) {
                if ($this->endsWith($name, '[]')) {
                    $name = substr($name, 0, strlen($name) - 2);
                    $queryParams[$name] = $value;
                }
            }
        }

        if ($queryParams) {
            $request = $request
                ->withQueryParams($queryParams);
        }

        return $request;
    }

    private function generateServerParams(
        $event,
        ContextInterface $context
    ): array {
        return [];
    }

    private function endsWith($haystack, $needle): bool
    {
        $length = strlen($needle);

        if (!$length) {
            return true;
        }

        return substr($haystack, -$length) === $needle;
    }
}
