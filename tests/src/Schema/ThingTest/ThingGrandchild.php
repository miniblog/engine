<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests\Schema\ThingTest;

use const null;

class ThingGrandchild extends ThingChild
{
    /** @phpstan-ignore-next-line */
    private ?string $baz;

    /** @phpstan-ignore-next-line */
    private ?string $qux;

    public function __construct()
    {
        parent::__construct();

        $this
            ->setBaz(null)
            ->setQux(null)
        ;
    }

    /** @return static */
    public function setBaz(?string $value): self
    {
        $this->baz = $value;
        return $this;
    }

    /** @return static */
    public function setQux(?string $value): self
    {
        $this->qux = $value;
        return $this;
    }
}
