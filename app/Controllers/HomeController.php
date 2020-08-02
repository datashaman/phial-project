<?php

declare(strict_types=1);

namespace App\Controllers;

use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;

class HomeController
{
    public function hello(): ResponseInterface
    {
        return new TextResponse('Hello World');
    }
}
