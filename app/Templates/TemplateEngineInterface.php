<?php

declare(strict_types=1);

namespace App\Templates;

interface TemplateEngineInterface
{
    public function render(string $template, array $params = []): string;
}
