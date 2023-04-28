<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\Registry;
use OutOfBoundsException;
use RuntimeException;
use Throwable;

use function array_filter;
use function array_slice;
use function count;
use function end;
use function implode;
use function is_array;
use function ksort;
use function passthru;
use function reset;
use function strstr;

use const false;
use const null;
use const PHP_EOL;
use const true;

class Console
{
    private Registry $registry;

    /**
     * @phpstan-var array<class-string<AbstractCommand>>
     */
    private array $commands;

    /**
     * @phpstan-param array<class-string<AbstractCommand>> $commands
     */
    public function __construct(Registry $registry, array $commands)
    {
        $this
            ->setRegistry($registry)
            ->setCommands($commands)
        ;
    }

    /**
     * @param string|string[] $message
     */
    public function writeLn($message): void
    {
        if (is_array($message)) {
            $message = implode(PHP_EOL, $message);
        }

        // phpcs:ignore
        echo $message . PHP_EOL;
    }

    // @todo Create a command from this.
    private function displayHelp(): int
    {
        $lines = [
            // ASCII art created using https://www.kammerl.de/ascii/AsciiSignature.php
            <<<END
            _|      _|  _|            _|  _|        _|
            _|_|  _|_|      _|_|_|        _|_|_|    _|    _|_|      _|_|_|
            _|  _|  _|  _|  _|    _|  _|  _|    _|  _|  _|    _|  _|    _|
            _|      _|  _|  _|    _|  _|  _|    _|  _|  _|    _|  _|    _|
            _|      _|  _|  _|    _|  _|  _|_|_|    _|    _|_|      _|_|_|
                                                                        _|
                                                                    _|_|
            END,
            '',
            'Available commands:',
        ];

        $commandNamesGroupedByNamespace = [];

        foreach ($this->getCommands() as $commandClassName) {
            $commandName = $commandClassName::COMMAND_NAME;
            $namespace = strstr($commandName, ':', true) ?: '';
            $commandNamesGroupedByNamespace[$namespace][] = $commandName;
        }

        ksort($commandNamesGroupedByNamespace);

        foreach ($commandNamesGroupedByNamespace as $namespace => $commandNames) {
            if ('' !== $namespace) {
                $lines[] = "  {$namespace}";
            }

            foreach ($commandNames as $commandName) {
                $lines[] = "    {$commandName}";
            }
        }

        $lines[] = '';

        $this->writeLn($lines);

        return AbstractCommand::SUCCESS;
    }

    /**
     * @throws OutOfBoundsException If the command does not exist
     * @throws RuntimeException If there are multiple commands with the same name
     */
    private function createCommandByCommandName(string $name): AbstractCommand
    {
        $matchingCommands = array_filter($this->getCommands(), function (string $commandClassName) use ($name): bool {
            return $name === $commandClassName::COMMAND_NAME;
        });

        $numMatchingCommands = count($matchingCommands);

        if (!$numMatchingCommands) {
            throw new OutOfBoundsException("The command, `{$name}`, does not exist");
        }

        if ($numMatchingCommands > 1) {
            throw new RuntimeException('There are multiple commands with the same name');
        }

        /** @phpstan-var class-string<AbstractCommand> */
        $commandClassName = reset($matchingCommands);

        return new $commandClassName($this);
    }

    /**
     * @phpstan-param CommandOptionsArray $options
     */
    public function invokeCommand(
        string $commandName,
        array $options = []
    ): int {
        $exitCode = AbstractCommand::SUCCESS;
        $message = null;

        try {
            $command = $this->createCommandByCommandName($commandName);
            $exitCode = $command($options);
        } catch (CommandFailedException $ex) {
            $exitCode = $ex->getExitCode();
            $message = $ex->getMessage();
        } catch (Throwable $t) {
            $exitCode = AbstractCommand::FAILURE;
            $message = $t->getMessage();
        }

        if (AbstractCommand::SUCCESS !== $exitCode) {
            $message = $message
                ? "{$commandName}: {$message}"
                : "Command `{$commandName}` failed with exit code `{$exitCode}`."
            ;

            $this->writeLn("\033[97;41m[ERROR] {$message}\033[0m");
        }

        return $exitCode;
    }

    /**
     * @throws CommandFailedException If it failed to execute the command
     */
    public function passthru(string $command): self
    {
        // By default, execute the command at the root of the project.
        /** @phpstan-var ConfigArray */
        $config = $this->getRegistry()->get('config');
        $command = "cd {$config['projectDir']} && {$command}";

        $this->writeLn("> {$command}");

        $resultCode = null;
        $successful = false;
        $message = null;

        try {
            // PHPStan is wrong about this.  According to the PHP manual, `passthru()` "Returns null on success or false
            // on failure".
            /** @phpstan-ignore-next-line */
            $successful = null === passthru($command, $resultCode);

            /** @phpstan-var bool $successful Because we just ignored the line that creates the variable */

            if ($successful && AbstractCommand::SUCCESS !== $resultCode) {
                $successful = false;
            }
        } catch (Throwable $t) {
            $successful = false;
            $message = $t->getMessage();
        }

        if (!$successful) {
            throw new CommandFailedException($message, $resultCode);
        }

        return $this;
    }

    public function handleRequest(): int
    {
        /** @var HttpRequest */
        $request = $this->getRegistry()->get('request');
        /** @var array<string,string> */
        $argv = $request->server['argv'];
        $numScriptNameParts = 2;
        $scriptNameParts = array_slice($argv, 0, $numScriptNameParts);

        if ($numScriptNameParts !== count($scriptNameParts)) {
            return $this->displayHelp();
        }

        $commandName = end($scriptNameParts);
        $options = array_slice($argv, $numScriptNameParts);

        return $this->invokeCommand($commandName, $options);
    }

    private function setRegistry(Registry $registry): self
    {
        $this->registry = $registry;
        return $this;
    }

    public function getRegistry(): Registry
    {
        return $this->registry;
    }

    /**
     * @phpstan-param array<class-string<AbstractCommand>> $commands
     */
    private function setCommands(array $commands): self
    {
        $this->commands = $commands;
        return $this;
    }

    /**
     * @phpstan-return array<class-string<AbstractCommand>>
     */
    public function getCommands(): array
    {
        return $this->commands;
    }
}
