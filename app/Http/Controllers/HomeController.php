<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ServerRequestInterface;

class HomeController
{
    public function index(ServerRequestInterface $request): TextResponse
    {
        return new TextResponse('Welcome');
    }
}
