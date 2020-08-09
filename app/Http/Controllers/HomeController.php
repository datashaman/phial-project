<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Fig\Http\Message\StatusCodeInterface;

class HomeController implements StatusCodeInterface
{
    public function index(): TextResponse
    {
        return new TextResponse('Welcome');
    }

    public function exception(): TextResponse
    {
        abort(self::STATUS_INTERNAL_SERVER_ERROR);
    }

    public function hello(string $name): TextResponse
    {
        return new TextResponse("Hello $name");
    }

    public function json(ServerRequestInterface $request): JsonResponse
    {
        return new JsonResponse($request->getParsedBody());
    }
}
