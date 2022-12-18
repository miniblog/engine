<?php

declare(strict_types=1);

namespace Miniblog\Engine\Command;

class RefreshContentCommand extends AbstractCommand
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'refresh-content';

    public function __invoke(): int
    {
        $this->getConsole()->invokeCommand('compile-project-error-pages');

        return self::SUCCESS;
    }
}
