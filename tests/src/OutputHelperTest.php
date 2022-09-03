<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use DanBettles\Marigold\OutputHelper\OutputHelperInterface;

class OutputHelperTest extends AbstractTestCase
{
    public function testIsAnOutputHelper(): void
    {
        $this->assertTrue($this->getTestedClass()->implementsInterface(OutputHelperInterface::class));
    }
}
