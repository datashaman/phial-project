<?php

declare(strict_types=1);

namespace App\Providers;

use App\Templates\LatteTemplateEngine;
use App\Templates\TemplateEngineInterface;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;

class TemplateServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [
            TemplateEngineInterface::class => fn(ContainerInterface $container) =>
                new LatteTemplateEngine(
                    $container->get('templateBaseDirectory'),
                    $container->get('templateTempDirectory')
                ),
        ];
    }

    public function getExtensions()
    {
        return [
        ];
    }
}
