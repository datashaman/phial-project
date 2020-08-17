<?php

declare(strict_types=1);

use function DI\env;

return [
    'dynamodb' => [
        'table' => env(
            'CACHE_DYNAMODB_TABLE',
            'PhialProjectCache'
        ),
    ],
];
