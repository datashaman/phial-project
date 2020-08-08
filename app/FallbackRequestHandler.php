<?php

declare(strict_types=1);

namespace App;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FallbackRequestHandler implements RequestHandlerInterface, StatusCodeInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        abort(self::STATUS_NOT_FOUND);
    }
}
