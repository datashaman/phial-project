<?php

declare(strict_types=1);

/** @var FastRoute\RouteCollector $r */

$r->addRoute('GET', '/{name}', 'App\Controllers\HomeController::hello');
$r->addRoute('GET', '/', 'App\Controllers\HomeController::index');
