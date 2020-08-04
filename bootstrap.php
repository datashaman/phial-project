<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/cache/CompiledContainer.php';

use Datashaman\Phial\RuntimeHandlerInterface;

use Invoker\Invoker;
use Invoker\InvokerInterface;
use Invoker\ParameterResolver\Container\ParameterNameContainerResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;

$container = new CompiledContainer();

$invoker = new Invoker(null, $container);

$parameterResolver = $invoker->getParameterResolver();

$parameterResolver->prependResolver(
    new ParameterNameContainerResolver($container)
);

$parameterResolver->prependResolver(
    new TypeHintContainerResolver($container)
);

$invoker->call(RuntimeHandlerInterface::class);
