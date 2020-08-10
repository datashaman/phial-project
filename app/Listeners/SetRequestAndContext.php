<?php

declare(strict_types=1);

namespace App\Listeners;

use Datashaman\Phial\Lambda\ContextInterface;
use Datashaman\Phial\Http\Events\RequestEvent;
use DI\Container;
use Psr\Http\Message\ServerRequestInterface;

class SetRequestAndContext
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function __invoke(RequestEvent $event): void
    {
        $this->container->set(ServerRequestInterface::class, $event->request);
        $this->container->set(ContextInterface::class, $event->context);
    }
}
