<?php

declare(strict_types=1);

use Monolog\Logger;

return [
    'format' => "%level_name% %message% %context% %extra%\n",
    'level' => Logger::DEBUG,
    'stream' => 'php://stderr',
];
