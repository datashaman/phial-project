<?php

declare(strict_types=1);

use Monolog\Logger;

return [
    'log.format' => "%channel%.%level_name%: %message% %context% %extra%\n",
    'log.level' => Logger::DEBUG,
    'log.stream' => 'php://stderr',
];
