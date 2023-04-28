<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use DanBettles\Marigold\Registry;
use Miniblog\Engine\AbstractCommand;
use Miniblog\Engine\Console;

use const PHP_EOL;

class ConsoleTest extends AbstractTestCase
{
    public function testIsInstantiable(): void
    {
        $registry = $this->createStub(Registry::class);
        /** @phpstan-var array<class-string<AbstractCommand>> Because we needn't create test-specific classes for this */
        $commands = ['App\Command\Foo'];
        $console = new Console($registry, $commands);

        $this->assertSame($registry, $console->getRegistry());
        $this->assertSame($commands, $console->getCommands());
    }

    public function testWritelnOutputsAMessageFollowedByANewline(): void
    {
        $expectedMessage = 'foo';

        $this->expectOutputString($expectedMessage . PHP_EOL);

        (new Console($this->createStub(Registry::class), []))
            ->writeLn($expectedMessage)
        ;
    }
}
