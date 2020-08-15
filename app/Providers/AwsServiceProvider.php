<?php

declare(strict_types=1);

namespace App\Providers;

use AsyncAws\Core\Configuration;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;

class AwsServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            Configuration::class => fn(ContainerInterface $container) =>
                Configuration::create($container->get('awsCoreConfiguration')),
        ];
    }

    public function getExtensions()
    {
        return [];
    }
}
