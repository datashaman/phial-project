<?php

require_once __DIR__ . '/vendor/autoload.php';

use Datashaman\Phial\RuntimeHandlerInterface;

use DI\Container;
use DI\ContainerBuilder;

use Invoker\Invoker;
use Invoker\InvokerInterface;
use Invoker\ParameterResolver\Container\ParameterNameContainerResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;

use Psr\Container\ContainerInterface;

class Application
{
    private InvokerInterface $invoker;

    public function __construct()
    {
        $container = $this->createContainer();
        $this->addServiceProviders($container);

        $this->invoker = $this->createInvoker($container);
    }

    public function __invoke(): void
    {
        $this->invoker->call(RuntimeHandlerInterface::class);
    }

    private function createContainer(): ContainerInterface
    {
        $builder = new ContainerBuilder();
        $builder->useAutowiring(false);

        $this->addDefinitions($builder);

        $container = $builder->build();
        $this->addServiceProviders($container);

        return $container;
    }

    private function addDefinitions(ContainerBuilder $builder): void
    {
        $directory = new RecursiveDirectoryIterator(
            __DIR__ . '/config',
            FilesystemIterator::SKIP_DOTS
        );

        $iterator = new RecursiveIteratorIterator($directory);

        foreach ($iterator as $name => $object) {
            $builder->addDefinitions($name);
        }
    }

    private function addServiceProviders(ContainerInterface $container): void
    {
        foreach ($container->get('app.providers') as $providerClass) {
            $provider = new $providerClass();
            $builder->addDefinitions($provider->getFactories());
        }
    }

    private function createInvoker(ContainerInterface $container): InvokerInterface
    {
        $invoker = new Invoker(null, $container);

        $parameterResolver = $invoker->getParameterResolver();
        $parameterResolver->prependResolver(
            new ParameterNameContainerResolver($container)
        );
        $parameterResolver->prependResolver(
            new TypeHintContainerResolver($container)
        );

        return $invoker;
    }
}

(new Application())();
