<?php

declare(strict_types=1);

namespace App\Handlers;

use Datashaman\Phial\ContextInterface;
use Psr\Log\LoggerInterface;

class Handler
{
    public function __invoke(array $event, ContextInterface $context)
    {
        $context->getLogger()->debug('Event', ['event' => $event]);
    }
}
