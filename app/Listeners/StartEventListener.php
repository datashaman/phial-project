<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Controllers\HomeController;
use Datashaman\Phial\Events\StartEvent;
use League\Route\RouteCollectionInterface;
use Psr\Log\LoggerInterface;

class StartEventListener
{
    private RouteCollectionInterface $router;
    private LoggerInterface $logger;

    public function __construct(RouteCollectionInterface $router, LoggerInterface $logger)
    {
        $this->router = $router;
        $this->logger = $logger;
    }

    public function __invoke(StartEvent $event): void
    {
        $this->logger->debug('Received start event, adding routes');
        $this->router->map('GET', '/', [HomeController::class, 'hello']);
    }
}
