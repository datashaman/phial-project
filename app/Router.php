<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ServerRequestInterface;

class Router extends \League\Route\Router
{
    /**
     * Prepare all routes, build name index and filter out none matching
     * routes before being passed off to the parser.
     *
     * @param ServerRequestInterface $request
     */
    protected function prepRoutes(ServerRequestInterface $request): void
    {
        if ($this->dataGenerator->getData() !== [[], []]) {
            parent::prepRoutes($request);
        }
    }
}
