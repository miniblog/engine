<?php

declare(strict_types=1);

namespace Miniblog\Engine\Command;

/**
 * Runs all automated tests; lints all the code; performs static analysis on the PHP.
 */
class CheckQualityCommand extends AbstractCommand
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'dev:check-quality';

    public function __invoke(): int
    {
        $this
            ->getConsole()
            ->invokeCommand('dev:test')
            ->passthru('vendor/bin/phpcs --standard=phpcs.xml')
            ->passthru('vendor/bin/phpstan --ansi')
        ;

        return self::SUCCESS;
    }
}
