<?php

declare(strict_types=1);

namespace Miniblog\Engine\Command;

use Miniblog\Engine\AbstractCommand;

class RefreshContentCommand extends AbstractCommand
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'refresh';

    public function __invoke(array $options = []): int
    {
        return $this->getConsole()->invokeCommand('compile-project-error-pages');
    }
}
