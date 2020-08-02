<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Controllers\HomeController;
use Datashaman\Phial\ContextInterface;
use Datashaman\Phial\Events\StartEvent;
use DI\Container;
use League\Route\Router;
use Psr\Http\Message\ServerRequestInterface;

class StartEventListener
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function __invoke(StartEvent $event): void
    {
        $this->router->map('GET', '/', [HomeController::class, 'hello']);
    }
}
