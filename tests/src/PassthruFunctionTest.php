<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;

use function passthru;

use const null;

class PassthruFunctionTest extends AbstractTestCase
{
    /** @return array<mixed[]> */
    public function providesCommands(): array
    {
        return [
            [
                0,
                null,
                'exit 0',
            ],
            [
                1,
                null,
                'exit 1',
            ],
            [
                127,
                null,
                'foobarbazquxquux',  /* Non-existent command. */
            ],
            [
                127,
                null,
                ',..:::dslkfjsflksjkl^&^&*',  /* Any old rubbish. */
            ],
            [
                0,
                null,
                ' ',
            ],
        ];
    }

    /**
     * @dataProvider providesCommands
     * @param null|bool $expectedReturnValue
     */
    public function testExecutesACommand(
        int $expectedExitCode,
        $expectedReturnValue,
        string $command
    ): void {
        $exitCode = null;
        /** @phpstan-ignore-next-line */
        $returnValue = passthru($command, $exitCode);

        $this->assertSame($expectedExitCode, $exitCode);
        $this->assertSame($expectedReturnValue, $returnValue);
    }
}
