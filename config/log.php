<?php

declare(strict_types=1);

use Monolog\Logger;

return [
    'logFormat' => "%level_name% %message% %context% %extra%\n",
    'logLevel' => Logger::DEBUG,
    'logStream' => 'php://stderr',
];
