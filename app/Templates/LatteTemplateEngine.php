<?php

declare(strict_types=1);

namespace App\Templates;

use Latte\Engine;
use Latte\Loaders\FileLoader;

class LatteTemplateEngine implements TemplateEngineInterface
{
    private Engine $engine;

    public function __construct(
        string $baseDirectory,
        string $tempDirectory
    )
    {
        $this->engine = (new Engine())
            ->setLoader(new FileLoader($baseDirectory))
            ->setTempDirectory($tempDirectory);

    }

    public function render(string $template, array $params = []): string
    {
        return $this->engine->renderToString($template, $params);
    }
}
