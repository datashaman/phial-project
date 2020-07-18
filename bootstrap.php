<?php

require_once 'vendor/autoload.php';

use Datashaman\Phial\RuntimeHandlerInterface;

use Invoker\Invoker;
use Invoker\ParameterResolver\Container\ParameterNameContainerResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;

function task_path(string $path): ?string
{
    return realpath(
        sprintf(
            '%s%s%s',
            getenv('LAMBDA_TASK_ROOT'),
            DIRECTORY_SEPARATOR,
            $path
        )
    ) ?: null;
}

$container = ($containerPath = task_path('container.php'))
    ? require_once $containerPath
    : null;

$invoker = new Invoker(null, $container);

if ($container) {
    $parameterResolver = $invoker->getParameterResolver();
    $parameterResolver->prependResolver(
        new ParameterNameContainerResolver($container)
    );
    $parameterResolver->prependResolver(
        new TypeHintContainerResolver($container)
    );
}

/** @var callable $callable */
$callable = RuntimeHandlerInterface::class;

$invoker->call($callable);
