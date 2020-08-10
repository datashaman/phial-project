<?php

declare(strict_types=1);

namespace App\Caches;

use Exception;

class InvalidArgumentException extends Exception implements \Psr\SimpleCache\InvalidArgumentException
{
}
