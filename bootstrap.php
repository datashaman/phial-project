<?php

require_once __DIR__ . '/vendor/autoload.php';

use Datashaman\Phial\RuntimeHandler;
use DI\ContainerBuilder;
use Invoker\Invoker;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\ResolverChain;
use Psr\Log\LoggerInterface;

define('BASE_DIR', getcwd());

$builder = new ContainerBuilder();
$builder->enableDefinitionCache();

$directory = new RecursiveDirectoryIterator(
    'config',
    FilesystemIterator::SKIP_DOTS
);

$iterator = new RecursiveIteratorIterator($directory);

foreach ($iterator as $name => $object) {
    $builder->addDefinitions($name);
}

$config = include_once 'config/app.php';

$providers = array_map(
    fn($providerClass) => new $providerClass(),
    $config['providers']
);

foreach ($providers as $provider) {
    $builder->addDefinitions($provider->getFactories());
}

$container = $builder->build();

foreach ($providers as $provider) {
    $extensions = $provider->getExtensions();

    foreach ($extensions as $key => $callable) {
        $previous = $container->get($key);
        $extended = call_user_func($callable, $container, $previous);
        $container->set($key, $extended);
    }
}

$invoker = new Invoker(null, $container);

/** @var ResolverChain */
$resolver = $invoker->getParameterResolver();

$resolver
    ->prependResolver(
        new TypeHintContainerResolver($container)
    );

/**
 * @var callable
 */
$handler = RuntimeHandler::class;

$invoker->call($handler);
