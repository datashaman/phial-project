<?php

declare(strict_types=1);

/** @var FastRoute\RouteCollector $r */

$r->addRoute('GET', '/exception', 'App\Http\Controllers\HomeController::exception');
$r->addRoute('GET', '/hello/{name}', 'App\Http\Controllers\HomeController::hello');
$r->addRoute('POST', '/json', 'App\Http\Controllers\HomeController::json');
$r->addRoute('GET', '/database', 'App\Http\Controllers\HomeController::database');
$r->addRoute('GET', '/env', 'App\Http\Controllers\HomeController::env');
$r->addRoute('GET', '/', 'App\Http\Controllers\HomeController::index');
