<?php

declare(strict_types=1);

namespace App\Templates;

use Datashaman\Phial\ConfigInterface;
use Latte\Engine;
use Latte\Loaders\FileLoader;

class LatteTemplateEngine implements TemplateEngineInterface
{
    private Engine $engine;

    public function __construct(
        ConfigInterface $config
    ) {
        $this->engine = (new Engine())
            ->setLoader(new FileLoader($config->get('templates.baseDirectory')))
            ->setTempDirectory($config->get('templates.tempDirectory'));
    }

    public function render(string $template, array $params = []): string
    {
        return $this->engine->renderToString($template, $params);
    }
}
