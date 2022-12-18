<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use DanBettles\Marigold\Registry;
use Miniblog\Engine\Console;

use const PHP_EOL;

class ConsoleTest extends AbstractTestCase
{
    public function testIsConstructedFromARegistry(): void
    {
        $registry = $this->createStub(Registry::class);
        $console = new Console($registry);

        $this->assertSame($registry, $console->getRegistry());
    }

    public function testWritelnOutputsAMessageFollowedByANewline(): void
    {
        $expectedMessage = 'foo';

        $this->expectOutputString($expectedMessage . PHP_EOL);

        $console = new Console($this->createStub(Registry::class));

        $console->writeLn($expectedMessage);
    }
}
