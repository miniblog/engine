<?php

declare(strict_types=1);

namespace Miniblog\Engine\Console;

use RuntimeException;

use function passthru as phpPassthru;

use const true;

function writeLn(string $message): void
{
    // phpcs:ignore
    echo ">> {$message}\n";
}

/**
 * @return never
 */
function succeeded(string $message = ''): void
{
    writeLn("[OK] {$message}");
    // phpcs:ignore
    exit(0);
}

/**
 * @return never
 */
function failed(string $message): void
{
    writeLn("[ERROR] {$message}");
    // phpcs:ignore
    exit(1);
}

/**
 * @throws RuntimeException If it failed to execute command.
 */
function passthru(
    string $command,
    bool $throwOnError = true
): int {
    writeLn("$ {$command}");

    $resultCode = 0;
    phpPassthru($command, $resultCode);

    if (0 !== $resultCode) {
        $message = 'Failed to execute command.';

        if ($throwOnError) {
            throw new RuntimeException($message);
        }

        writeLn("Warning: {$message}");
    }

    return $resultCode;
}
