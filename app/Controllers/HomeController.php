<?php

declare(strict_types=1);

namespace App\Controllers;

use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;

class HomeController
{
    public function index(): TextResponse
    {
        return new TextResponse('Welcome');
    }

    public function hello(string $name): TextResponse
    {
        return new TextResponse("Hello $name");
    }
}
