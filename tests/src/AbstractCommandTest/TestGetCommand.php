<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests\AbstractCommandTest;

use Miniblog\Engine\AbstractCommand;

class TestGetCommand extends AbstractCommand
{
    public const COMMAND_NAME = 'ignore';

    public function __invoke(): int
    {
        return self::SUCCESS;
    }
}
