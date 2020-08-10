<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use Northwoods\Broker\Broker;
use Northwoods\Middleware\LazyMiddlewareFactory;
use Datashaman\Phial\Http\RequestHandlerFactoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandlerFactory implements RequestHandlerFactoryInterface
{
    /**
     * @var array<string>
     * @psalm-var list<string>
     */
    private array $middleware;

    private ContainerInterface $container;

    /**
     * @param array<string> $middleware
     * @psalm-param list<string> $middleware
     */
    public function __construct(
        array $middleware,
        ContainerInterface $container
    ) {
        $this->middleware = $middleware;
        $this->container = $container;
    }

    public function createRequestHandler(): RequestHandlerInterface
    {
        $broker = new Broker();
        $factory = new LazyMiddlewareFactory($this->container);

        foreach ($this->middleware as $middleware) {
            $broker->append($factory->defer($middleware));
        }

        return $broker;
    }
}
