<?php

declare(strict_types=1);

namespace App;

use Datashaman\Phial\ContextInterface;
use Exception;

final class QueueHandler extends AbstractHandler
{
    /**
     * @param array<string|array> $event
     */
    public function __invoke($event, ContextInterface $context): string
    {
        return 'yo';
    }
}
