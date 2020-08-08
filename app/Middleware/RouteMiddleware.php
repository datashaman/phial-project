<?php

declare(strict_types=1);

namespace App\Middleware;

use Exception;
use FastRoute\Dispatcher;
use Fig\Http\Message\StatusCodeInterface;
use Invoker\InvokerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouteMiddleware implements MiddlewareInterface, StatusCodeInterface
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

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $resolved = $this->dispatcher->dispatch(
            $request->getMethod(),
            (string) $request->getUri()
        );

        $status = array_shift($resolved);

        $response = null;
        switch ($status) {
            case Dispatcher::FOUND:
                [$handler, $vars] = $resolved;

                $response = $this->invoker->call($handler, $vars);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                [$allowedMethods] = $resolved;
                abort(
                    'Method Not Allowed',
                    self::STATUS_METHOD_NOT_ALLOWED,
                    null,
                    [
                        'Allow' => implode(', ', $allowedMethods),
                    ]
                );
                break;
            case Dispatcher::NOT_FOUND:
                abort(self::STATUS_NOT_FOUND);
                break;
            default:
                $response = $handler->handle($request);
        }

        return $response;
    }
}
