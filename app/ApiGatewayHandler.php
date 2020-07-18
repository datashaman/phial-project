<?php

declare(strict_types=1);

namespace App;

use Datashaman\Phial\ContextInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;

abstract class ApiGatewayHandler extends AbstractHandler
{
    /**
     * @param array<string|array> $event
     */
    function __invoke(
        $event,
        ContextInterface $context,
        ServerRequestFactoryInterface $serverRequestFactory
    ): string {
        $request = $serverRequestFactory->createRequest(
            $event['httpMethod'],
        );

        $context->getLogger()->debug('Handle event', ['event' => $event]);

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

    private function generateServerVariables(
        $event,
        ContextInterface $context
    ): array {
    }
}
