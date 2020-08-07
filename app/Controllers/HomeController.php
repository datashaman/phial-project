<?php

declare(strict_types=1);

namespace App\Controllers;

use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;

class HomeController
{
    public function index(): ResponseInterface
    {
        return new TextResponse('Welcome');
    }

    public function hello(string $name): ResponseInterface
    {
        return new TextResponse("Hello $name");
    }
}
