<?php

declare(strict_types=1);

use Monolog\Logger;

use function DI\get;

return [
    // https://async-aws.com/integration/monolog.html
    'log.cloudwatch.handler' => [
        'group' => get('app.id'),
        'level' => Logger::DEBUG,
        'stream' => 'lambda',
    ],
];
