<?php

declare(strict_types=1);

namespace App;

use FastRoute\Dispatcher;
use Invoker\InvokerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandler implements RequestHandlerInterface
{
    private Dispatcher $dispatcher;
    private InvokerInterface $invoker;

    public function __construct(
        Dispatcher $dispatcher,
        InvokerInterface $invoker
    ) {
        $this->dispatcher = $dispatcher;
        $this->invoker = $invoker;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $resolved = $this->dispatcher->dispatch(
            $request->getMethod(),
            (string) $request->getUri()
        );

        $status = array_shift($resolved);

        switch ($status) {
            case Dispatcher::FOUND:
                [$handler, $vars] = $resolved;

                return $this->invoker->call($handler, $vars);
            case Dispatcher::METHOD_NOT_ALLOWED:
            case Dispatcher::NOT_FOUND:
        }

        dd($resolved);
    }
}
