<?php

declare(strict_types=1);

namespace App;

use Datashaman\Phial\ContextInterface;
use Negotiation\Negotiator;

abstract class AbstractHandler
{
    /**
     * @var Negotiator
     */
    private $negotiator;

    public function __construct(
        Negotiator $negotiator
    ) {
        $this->negotiator = $negotiator;
    }

    private function negotiate(
        ContextInterface $context,
        string $acceptHeader,
        array $priorities
    ): array {
        $acceptsType = $this
            ->negotiator
            ->getBest(
                $acceptHeader,
                [
                    'text/html',
                    'application/json',
                ]
            )
            ->getType();

        $context->getLogger()->debug('Accept Negotation', ['acceptHeader' => $acceptHeader, 'acceptsType' => $acceptsType]);

        return call_user_func($priorities[$acceptsType], $context);
    }
}
