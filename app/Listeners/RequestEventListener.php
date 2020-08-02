<?php

declare(strict_types=1);

namespace App\Listeners;

use Datashaman\Phial\ContextInterface;
use Datashaman\Phial\Events\RequestEvent;
use DI\Container;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class RequestEventListener
{
    private Container $container;
    private LoggerInterface $logger;

    public function __construct(Container $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
    }

    public function __invoke(RequestEvent $event): void
    {
        $this->logger->debug('Received request event, setting container values');
        $this->container->set(ServerRequestInterface::class, $event->request);
        $this->container->set(ContextInterface::class, $event->context);
    }
}
