<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use RuntimeException;
use Throwable;

use const null;

class CommandFailedException extends RuntimeException
{
    public function __construct(
        string $message = null,
        int $exitCode = null,
        Throwable $previous = null
    ) {
        if (null === $exitCode) {
            $exitCode = 1;
        }

        if (null === $message) {
            $message = "The command failed with exit code `{$exitCode}`.";
        }

        parent::__construct($message, $exitCode, $previous);
    }

    public function getExitCode(): int
    {
        return $this->getCode();
    }
}
