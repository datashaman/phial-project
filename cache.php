<?php

if ($argc !== 2) {
    echo "Usage: {$argv[0]} workingDir\n";
    exit(1);
}

$workingDir = realpath($argv[1]);

if ($workingDir === false) {
    echo "Working dir does not exist\n";
    exit(1);
}

chdir($workingDir);

require_once 'vendor/autoload.php';

use App\Http\Controllers\HomeController;
use App\Router;

use DI\Container;
use DI\ContainerBuilder;

use FastRoute\Dispatcher;

use Psr\Container\ContainerInterface;

$builder = new ContainerBuilder();

$builder->enableDefinitionCache();

$builder->enableCompilation('cache');
$builder->writeProxiesToFile(true, 'cache');

$directory = new RecursiveDirectoryIterator(
    'config',
    FilesystemIterator::SKIP_DOTS
);

$iterator = new RecursiveIteratorIterator($directory);

foreach ($iterator as $name => $object) {
    $builder->addDefinitions($name);
}

$config = include_once 'config/app.php';

foreach ($config['app.providers'] as $providerClass) {
    $provider = new $providerClass();
    $builder->addDefinitions($provider->getFactories());
}

$container = $builder->build();
$dispatcher = $container->get(Dispatcher::class);
