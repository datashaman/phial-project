<?php

declare(strict_types=1);

namespace App\Handlers;

use Datashaman\Phial\ContextInterface;
use Datashaman\Phial\FunctionHandlerInterface;

class EventReceivedHandler implements FunctionHandlerInterface
{
    public function handle(array $event, ContextInterface $context)
    {
        $context->getLogger()->debug('Event Received', ['event' => $event, 'context' => $context->toArray()]);
    }
}
