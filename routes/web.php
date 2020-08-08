<?php

declare(strict_types=1);

/** @var FastRoute\RouteCollector $r */

$r->addRoute('GET', '/exception', 'App\Http\Controllers\HomeController::exception');
$r->addRoute('GET', '/{name}', 'App\Http\Controllers\HomeController::hello');
$r->addRoute('GET', '/', 'App\Http\Controllers\HomeController::index');
