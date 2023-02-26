<?php

declare(strict_types=1);

namespace Miniblog\Engine\Command;

use Miniblog\Engine\AbstractCommand;

/**
 * Checks quality and then completely (re)builds the app.
 */
class BuildCommand extends AbstractCommand
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'dev:build';

    public function __invoke(): int
    {
        $this
            ->getConsole()
            ->invokeCommand('dev:check-quality')
            ->invokeCommand('dev:assemble-default-css')
            ->invokeCommand('refresh-content')
        ;

        return self::SUCCESS;
    }
}
