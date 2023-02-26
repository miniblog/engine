<?php

declare(strict_types=1);

namespace Miniblog\Engine\Command;

use Miniblog\Engine\AbstractCommand;

/**
 * Runs all automated tests.
 */
class TestCommand extends AbstractCommand
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'dev:test';

    public function __invoke(): int
    {
        $this->getConsole()->passthru('vendor/bin/phpunit --bootstrap=tests/src/.bootstrap.php --colors=always tests');

        return self::SUCCESS;
    }
}
