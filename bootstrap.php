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

$builder = new ContainerBuilder();

$builder->enableCompilation('/tmp');
$builder->enableDefinitionCache();
$builder->writeProxiesToFile(true, '/tmp/proxies');

$directory = new RecursiveDirectoryIterator(
    __DIR__ . '/config',
    FilesystemIterator::SKIP_DOTS
);

$iterator = new RecursiveIteratorIterator($directory);

foreach ($iterator as $name => $object) {
    $builder->addDefinitions($name);
}

$app = include_once __DIR__ . '/config/app.php';

foreach ($app['app.providers'] as $providerClass) {
    $provider = new $providerClass();
    $builder->addDefinitions($provider->getFactories());
}

$container =  $builder->build();

$invoker = new Invoker(null, $container);

$parameterResolver = $invoker->getParameterResolver();

$parameterResolver->prependResolver(
    new ParameterNameContainerResolver($container)
);

$parameterResolver->prependResolver(
    new TypeHintContainerResolver($container)
);

$invoker->call(RuntimeHandlerInterface::class);
