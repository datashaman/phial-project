<?php

declare(strict_types=1);

use function DI\env;

return [
    'cache.dynamodb.tableName' => env(
        'CACHE_DYNAMODB_TABLE_NAME',
        'PhialProjectCache'
    ),
];
