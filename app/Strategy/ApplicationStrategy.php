<?php

declare(strict_types=1);

namespace App\Strategy;

use Datashaman\Phial\ContextInterface;
use Invoker\InvokerInterface;
use Laminas\Diactoros\Response\TextResponse;
use League\Route\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Container\ContainerInterface;

class ApplicationStrategy extends \League\Route\Strategy\ApplicationStrategy
{
    private InvokerInterface $invoker;

    public function __construct(InvokerInterface $invoker)
    {
        $this->invoker = $invoker;
    }

    /**
     * {@inheritdoc}
     */
    public function invokeRouteCallable(Route $route, ServerRequestInterface $request): ResponseInterface
    {
        $container = $this->getContainer();
        $controller = $route->getCallable($container);
        $response = $this->invoker->call($controller, $route->getVars());
        $response = $this->applyDefaultResponseHeaders($response);

        return $response;
    }
}
