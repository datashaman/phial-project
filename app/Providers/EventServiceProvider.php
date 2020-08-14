<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\SetRequestAndContext;
use Datashaman\Phial\Http\Events\RequestEvent;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class EventServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [];
    }

    public function getExtensions()
    {
        return [
            ListenerProviderInterface::class => function (ContainerInterface $container, $provider) {
                $provider->addService(RequestEvent::class, SetRequestAndContext::class);

                return $provider;
            },
        ];
    }
}
