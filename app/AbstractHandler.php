<?php

declare(strict_types=1);

namespace App;

use Invoker\InvokerInterface;

abstract class AbstractHandler
{
    /**
     * @var InvokerInterface
     */
    protected $invoker;

    public function __construct(
        InvokerInterface $invoker
    ) {
        $this->invoker = $invoker;
    }

}
