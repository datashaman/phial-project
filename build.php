<?php

require_once __DIR__ . '/vendor/autoload.php';

use DI\Container;
use DI\ContainerBuilder;

use Psr\Container\ContainerInterface;

$builder = new ContainerBuilder();

$builder->enableDefinitionCache();

$builder->enableCompilation(__DIR__ . '/cache');
$builder->writeProxiesToFile(true, __DIR__ . '/cache');

$directory = new RecursiveDirectoryIterator(
    __DIR__ . '/config',
    FilesystemIterator::SKIP_DOTS
);

$iterator = new RecursiveIteratorIterator($directory);

foreach ($iterator as $name => $object) {
    $builder->addDefinitions($name);
}

$config = include_once __DIR__ . '/config/app.php';

foreach ($config['app.providers'] as $providerClass) {
    $provider = new $providerClass();
    $builder->addDefinitions($provider->getFactories());
}

$builder->build();
