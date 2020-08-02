<?php

use App\Controllers\HomeController;

$router->map('GET', '/', [HomeController::class, 'hello']);
