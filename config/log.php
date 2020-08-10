<?php

declare(strict_types=1);

use Monolog\Logger;

return [
    'log.handler' => [
        'group' => 'phial-project',
        'level' => Logger::DEBUG,
        'stream' => 'lambda',
    ],
];
