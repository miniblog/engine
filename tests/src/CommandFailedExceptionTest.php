<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use Miniblog\Engine\CommandFailedException;
use RuntimeException;

use const null;

class CommandFailedExceptionTest extends AbstractTestCase
{
    public function testIsARuntimeexception(): void
    {
        $this->assertTrue($this->getTestedClass()->isSubclassOf(RuntimeException::class));
    }

    public function testGetexitcodeReturnsTheExitCode(): void
    {
        $message = 'foo';
        $exitCode = 1;
        $ex = new CommandFailedException($message, $exitCode);

        $this->assertSame($message, $ex->getMessage());

        $this->assertSame($exitCode, $ex->getExitCode());
        $this->assertSame($ex->getExitCode(), $ex->getCode());
    }

    /** @return array<mixed[]> */
    public function providesExceptions(): array
    {
        return [
            [
                'The command failed with exit code `1`.',
                1,
                new CommandFailedException(),
            ],
            [
                'Foo',
                1,
                new CommandFailedException('Foo'),
            ],
            [
                'Foo',
                127,
                new CommandFailedException('Foo', 127),
            ],
            [
                'The command failed with exit code `127`.',
                127,
                new CommandFailedException(null, 127),
            ],
            [
                'The command failed with exit code `1`.',
                1,
                new CommandFailedException(null, null),
            ],
        ];
    }

    /**
     * @dataProvider providesExceptions
     */
    public function testHasDefaultValues(
        string $message,
        int $exitCode,
        CommandFailedException $ex
    ): void {
        $this->assertSame($message, $ex->getMessage());

        $this->assertSame($exitCode, $ex->getExitCode());
        $this->assertSame($ex->getExitCode(), $ex->getCode());
    }
}
