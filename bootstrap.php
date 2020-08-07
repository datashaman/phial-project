<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/cache/CompiledContainer.php';

use Datashaman\Phial\RuntimeHandlerInterface;

use Invoker\Invoker;
use Invoker\ParameterResolver\Container\ParameterNameContainerResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\ResolverChain;

$container = new CompiledContainer();
$invoker = new Invoker(null, $container);

/** @var ResolverChain */
$resolver = $invoker->getParameterResolver();

$resolver
    ->prependResolver(
        new TypeHintContainerResolver($container)
    );

$invoker->call(RuntimeHandlerInterface::class);
