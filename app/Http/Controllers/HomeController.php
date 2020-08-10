<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Templates\TemplateEngineInterface;
use Datashaman\Phial\Lambda\ContextInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Fig\Http\Message\StatusCodeInterface;

class HomeController implements StatusCodeInterface
{
    private TemplateEngineInterface $engine;

    public function __construct(TemplateEngineInterface $engine)
    {
        $this->engine = $engine;
    }

    public function index(ServerRequestInterface $request): TextResponse
    {
        return new TextResponse('Welcome');
    }

    public function exception(): void
    {
        abort(self::STATUS_INTERNAL_SERVER_ERROR);
    }

    public function hello(string $name): HtmlResponse
    {
        return new HtmlResponse($this->engine->render('welcome.latte', ['name' => $name]));
    }
}
