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
    public function index(ServerRequestInterface $request): TextResponse
    {
        return new TextResponse('Welcome');
    }

    public function exception(): void
    {
        abort(self::STATUS_INTERNAL_SERVER_ERROR);
    }

    public function hello(string $name): TextResponse
    {
        return new TextResponse("Hello $name");
    }

    public function json(ServerRequestInterface $request): JsonResponse
    {
        return new JsonResponse(
            [
                'parsed' => $request->getParsedBody(),
                'body' => (string) $request->getBody(),
            ]
        );
    }

    public function env(ServerRequestInterface $request, ContextInterface $context): JsonResponse
    {
        $env = getenv();
        ksort($env);

        $logger = $context->getLogger();
        $logger->debug('Environment', $env);

        return new JsonResponse($env);
    }

    public function database(ServerRequestInterface $request, ContextInterface $context): JsonResponse
    {
        $tables = [];

        $db = new PDO(
            sprintf(
                'mysql:host=%s;dbname=%s',
                getenv('RDS_HOST'),
                getenv('RDS_DATABASE')
            ),
            getenv('RDS_USER'),
            getenv('RDS_PASSWORD')
        );

        foreach ($db->query('show tables') as $row) {
            $tables[] = $row;
        }

        $db = null;

        return new JsonResponse($tables);
    }

    public function template(string $name, TemplateEngineInterface $engine): HtmlResponse
    {
        return new HtmlResponse($engine->render('welcome.latte', ['name' => $name]));
    }
}
