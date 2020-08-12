<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Templates\TemplateEngineInterface;
use Datashaman\Phial\Lambda\ContextInterface;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use PDO;
use Psr\SimpleCache\CacheInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController implements StatusCodeInterface
{
    private TemplateEngineInterface $engine;
    private CacheInterface $cache;

    public function __construct(
        TemplateEngineInterface $engine,
        CacheInterface $cache
    ) {
        $this->engine = $engine;
        $this->cache = $cache;
    }

    public function index(ServerRequestInterface $request): TextResponse
    {
        return new TextResponse('Welcome');
    }

    public function exception(): void
    {
        abort(self::STATUS_INTERNAL_SERVER_ERROR);
    }

    public function hello(string $name, ContextInterface $context): JsonResponse
    {
        $logger = $context->getLogger();

        $this->cache->delete('xyz');

        if (!$this->cache->has('a')) {
            $logger->debug('Set a');
            $this->cache->set('a', 'AAA', 30);
        }

        $a = $this->cache->get('a');

        return new JsonResponse(
            [
                'a' => $a,
            ]
        );
    }

    public function env(ServerRequestInterface $request, ContextInterface $context)
    {
        $env = getenv();
        ksort($env);

        return new JsonResponse(
            [
                'context' => $context->toArray(),
                'env' => $env,
                'headers' => $request->getHeaders(),
            ]
        );
    }
}
