<?php

declare(strict_types=1);

namespace App;

use Invoker\InvokerInterface;

abstract class AbstractHandler
{
    protected \Invoker\InvokerInterface $invoker;

    public function __construct(
        InvokerInterface $invoker
    ) {
        $this->invoker = $invoker;
    }

}
