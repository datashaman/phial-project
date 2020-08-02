<?php

use Datashaman\Phial\ContextInterface;
use Invoker\InvokerInterface;
use Laminas\Diactoros\Response\TextResponse;
use League\Route\Route;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Container\ContainerInterface;

class Strategy extends ApplicationStrategy
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

$strategy = $container->get(ApplicationStrategy::class)->setContainer($container);
$router = (new Router())->setStrategy($strategy);

require_once __DIR__ . '/routes.php';

return $router;
