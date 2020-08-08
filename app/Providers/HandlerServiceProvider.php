<?php

declare(strict_types=1);

namespace App\Providers;

use Datashaman\Phial\ContextFactory;
use Datashaman\Phial\ContextFactoryInterface;
use Datashaman\Phial\RuntimeHandler;
use Datashaman\Phial\RuntimeHandlerInterface;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;

class HandlerServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            ContextFactoryInterface::class => fn(ContainerInterface $container) =>
                $container->get(ContextFactory::class),
            RuntimeHandlerInterface::class => fn(ContainerInterface $container) =>
                $container->get(RuntimeHandler::class),
        ];
    }

    public function getExtensions()
    {
        return [];
    }
}
