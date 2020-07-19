<?php

require_once 'vendor/autoload.php';

use Datashaman\Phial\RuntimeHandlerInterface;
use DI\ContainerBuilder;
use Invoker\Invoker;
use Invoker\ParameterResolver\Container\ParameterNameContainerResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;

$builder = new ContainerBuilder();
$builder->addDefinitions('config.php');
$container = $builder->build();
$invoker = new Invoker(null, $container);

$parameterResolver = $invoker->getParameterResolver();
$parameterResolver->prependResolver(
    new ParameterNameContainerResolver($container)
);
$parameterResolver->prependResolver(
    new TypeHintContainerResolver($container)
);

/** @var callable $callable */
$callable = RuntimeHandlerInterface::class;

$invoker->call($callable);
