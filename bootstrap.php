<?php

require_once __DIR__ . '/vendor/autoload.php';

use Datashaman\Phial\RuntimeHandlerInterface;
use DI\ContainerBuilder;
use Invoker\Invoker;
use Invoker\ParameterResolver\Container\ParameterNameContainerResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;

$builder = new ContainerBuilder();

$directory = new RecursiveDirectoryIterator(__DIR__ . '/config', FilesystemIterator::SKIP_DOTS);
$iterator = new RecursiveIteratorIterator($directory);

foreach ($iterator as $name => $object) {
    $builder->addDefinitions($name);
}

$container = $builder->build();
$invoker = new Invoker(null, $container);

$parameterResolver = $invoker->getParameterResolver();
$parameterResolver->prependResolver(
    new ParameterNameContainerResolver($container)
);
$parameterResolver->prependResolver(
    new TypeHintContainerResolver($container)
);

/**
 * @var callable $callable
*/
$callable = RuntimeHandlerInterface::class;

$invoker->call($callable);
