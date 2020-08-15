<?php

declare(strict_types=1);

use function DI\env;

return [
    'cacheDynamodbTableName' => env(
        'CACHE_DYNAMODB_TABLE_NAME',
        'PhialProjectCache'
    ),
];
