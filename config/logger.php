<?php

declare(strict_types=1);

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Logger;

use Psr\Log\LoggerInterface;

return [
    'logger.format' => "%channel%.%level_name%: %message% %context% %extra%\n",
    'logger.level' => Logger::DEBUG,
    'logger.stream' => 'php://stderr',

    LineFormatter::class => DI\create(LineFormatter::class)
        ->constructor(DI\get('logger.format'), null, false, true),
    LoggerInterface::class => DI\create(Logger::class)
        ->constructor(DI\get('app.id'))
        ->method('pushHandler', DI\get(StreamHandler::class)),
    StreamHandler::class => DI\create()
        ->constructor(DI\get('logger.stream'), DI\get('logger.level'))
        ->method('pushProcessor', DI\get(PsrLogMessageProcessor::class))
        ->method('setFormatter', DI\get(LineFormatter::class)),
];
