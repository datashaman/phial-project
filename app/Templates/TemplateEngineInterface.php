<?php

declare(strict_types=1);

namespace App\Templates;

interface TemplateEngineInterface
{
    /**
     * @param array<string, mixed> $params
     */
    public function render(string $template, array $params = []): string;
}
