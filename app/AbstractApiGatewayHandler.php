<?php

declare(strict_types=1);

namespace App;

use Datashaman\Phial\ContextInterface;
use Exception;
use Invoker\InvokerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\XmlResponse;
use Negotiation\Accept;
use Negotiation\Negotiator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractApiGatewayHandler extends AbstractHandler
{
    private \Negotiation\Negotiator $negotiator;

    private \Psr\Http\Message\ServerRequestFactoryInterface $serverRequestFactory;

    private \Psr\Http\Message\StreamFactoryInterface $streamFactory;

    public function __construct(
        InvokerInterface $invoker,
        Negotiator $negotiator,
        ServerRequestFactoryInterface $serverRequestFactory,
        StreamFactoryInterface $streamFactory
    ) {
        parent::__construct($invoker);

        $this->negotiator = $negotiator;
        $this->serverRequestFactory = $serverRequestFactory;
        $this->streamFactory = $streamFactory;
    }

    public function html(ServerRequestInterface $request, ContextInterface $context): HtmlResponse
    {
        throw new Exception('Not implemented');
    }

    public function json(ServerRequestInterface $request, ContextInterface $context): JsonResponse
    {
        throw new Exception('Not implemented');
    }

    public function xml(ServerRequestInterface $request, ContextInterface $context): XmlResponse
    {
        throw new Exception('Not implemented');
    }

    /**
     * @param array<string,mixed> $event
     */
    function __invoke(
        array $event,
        ContextInterface $context,
        LoggerInterface $logger
    ): string {
        $logger->debug('Handle event', ['event' => $event, 'context' => $context->toArray()]);

        $request = $this->createServerRequest($event, $context);

        $response = $this->negotiate(
            $request,
            $context,
            [
                'application/json' => [$this, 'json'],
                'application/xml' => [$this, 'xml'],
                'text/html' => [$this, 'html'],
            ],
            'application/json'
        );

        $logger->debug('Response', ['response' => $response]);

        return $this->generateResponseJson($response);
    }

    private function generateResponseJson(ResponseInterface $response): string
    {
        $headers = [];

        foreach ($response->getHeaders() as $name => $value) {
            $headers[$name] = implode(', ', $value);
        }

        $payload = [
            'statusCode' => $response->getStatusCode(),
            'body' => (string) $response->getBody(),
            'headers' => $headers,
        ];

        return json_encode($payload, JSON_THROW_ON_ERROR);
    }

    /**
     * @param array<string,mixed> $event
     */
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

        if (!is_null($event['body'])) {
            $body = $event['isBase64Encoded']
                ? base64_decode($event['body'])
                : $event['body'];
            $stream = $this->streamFactory->createStream();
            $stream->write($body);
            $request = $request->withBody($stream);
        }

        return $request;
    }

    /**
     * @param array<string,mixed> $event
     *
     * @return array<string,mixed>
     */
    private function generateServerParams(
        array $event,
        ContextInterface $context
    ): array {
        return [];
    }

    private function endsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);

        if (!$length) {
            return true;
        }

        return substr($haystack, -$length) === $needle;
    }

    /**
     * @param array<string,array<int,callable|string>> $priorities
     */
    protected function negotiate(
        ServerRequestInterface $request,
        ContextInterface $context,
        array $priorities,
        string $default
    ): ResponseInterface {
        $accept = $this->accept($request, $default);
        $context->getLogger()->debug('Content Negotation', ['accept' => $accept]);

        /** @var callable $callable */
        $callable = $priorities[$accept];

        return $this->invoker->call(
            $callable,
            [
                'request' => $request,
                'context' => $context,
            ]
        );
    }

    private function accept(
        ServerRequestInterface $request,
        string $default
    ): string {
        $header = $request->getHeaderLine('Accept') ?? $default;

	/** @var Accept|null */
        $best = $this
            ->negotiator
            ->getBest(
                $header,
                [
                    'text/html',
                    'application/json',
                    'application/xml',
                ]
            );

        if (!$best) {
            return $default;
        }

        return $best->getType();
    }
}
