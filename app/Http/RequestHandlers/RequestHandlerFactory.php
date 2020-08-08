<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use Datashaman\Phial\RequestHandlerFactoryInterface;
use DI\FactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandlerFactory implements RequestHandlerFactoryInterface
{
    private array $middleware;
    private RequestHandlerInterface $fallbackRequestHandler;
    private FactoryInterface $factory;

    public function __construct(
        array $middleware,
        RequestHandlerInterface $fallbackRequestHandler,
        FactoryInterface $factory
    ) {
        $this->middleware = $middleware;
        $this->fallbackRequestHandler = $fallbackRequestHandler;
        $this->factory = $factory;
    }

    public function createRequestHandler(): RequestHandlerInterface
    {
        return new QueueRequestHandler(
            $this->middleware,
            $this->fallbackRequestHandler,
            $this->factory
        );
    }
}
