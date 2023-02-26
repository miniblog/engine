<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests\Schema\ThingTest;

use Miniblog\Engine\Schema\Thing;

use const null;

class ThingChild extends Thing
{
    /** @phpstan-ignore-next-line */
    private ?string $foo;

    /** @phpstan-ignore-next-line */
    private ?string $bar;

    public function __construct()
    {
        parent::__construct();

        $this
            ->setFoo(null)
            ->setBar(null)
        ;
    }

    /** @return static */
    public function setFoo(?string $value): self
    {
        $this->foo = $value;
        return $this;
    }

    /** @return static */
    public function setBar(?string $value): self
    {
        $this->bar = $value;
        return $this;
    }
}
