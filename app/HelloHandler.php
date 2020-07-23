<?php

declare(strict_types=1);

namespace App;

use Datashaman\Phial\ContextInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\XmlResponse;
use Psr\Http\Message\ServerRequestInterface;

final class HelloHandler extends AbstractApiGatewayHandler
{
    public function html(ServerRequestInterface $request, ContextInterface $context): HtmlResponse
    {
        return new HtmlResponse('hello world');
    }

    public function json(ServerRequestInterface $request, ContextInterface $context): JsonResponse
    {
        return new JsonResponse('hello world');
    }

    public function xml(ServerRequestInterface $request, ContextInterface $context): XmlResponse
    {
        return new XmlResponse('<hello name="world"/>');
    }
}
