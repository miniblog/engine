<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\Registry;
use Miniblog\Engine\Command\AbstractCommand;
use Miniblog\Engine\Command\AssembleDefaultCssCommand;
use Miniblog\Engine\Command\BuildCommand;
use Miniblog\Engine\Command\CheckQualityCommand;
use Miniblog\Engine\Command\CompileProjectErrorPagesCommand;
use Miniblog\Engine\Command\RefreshContentCommand;
use Miniblog\Engine\Command\TestCommand;
use OutOfBoundsException;
use RuntimeException;
use Throwable;

use function array_keys;
use function array_key_exists;
use function explode;
use function implode;
use function is_array;
use function ksort;
use function passthru;
use function strpos;

use const false;
use const null;
use const PHP_EOL;
use const true;

class Console
{
    /**
     * @var array<string,class-string<AbstractCommand>>
     */
    private const COMMAND_CLASSES = [
        AssembleDefaultCssCommand::COMMAND_NAME => AssembleDefaultCssCommand::class,
        BuildCommand::COMMAND_NAME => BuildCommand::class,
        CheckQualityCommand::COMMAND_NAME => CheckQualityCommand::class,
        TestCommand::COMMAND_NAME => TestCommand::class,
        CompileProjectErrorPagesCommand::COMMAND_NAME => CompileProjectErrorPagesCommand::class,
        RefreshContentCommand::COMMAND_NAME => RefreshContentCommand::class,
    ];

    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->setRegistry($registry);
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

    /**
     * @return never
     */
    private function succeeded(string $message = null): void
    {
        if (null !== $message) {
            $this->writeLn("\033[30;42m[OK] {$message}\033[0m");
        }

        // phpcs:ignore
        exit(AbstractCommand::SUCCESS);
    }

    /**
     * @return never
     */
    private function failed(
        string $message,
        int $exitCode = AbstractCommand::FAILURE
    ): void {
        $this->writeLn("\033[97;41m[ERROR] {$message}\033[0m");
        // phpcs:ignore
        exit($exitCode);
    }

    /**
     * @return never
     */
    private function displayHelp(): void
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

        /** @var string $commandName */
        foreach (array_keys(self::COMMAND_CLASSES) as $commandName) {
            $namespace = '';

            if (false !== strpos($commandName, ':')) {
                list($namespace) = explode(':', $commandName);
            }

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

        $this->succeeded();
    }

    /**
     * @throws OutOfBoundsException If the command does not exist.
     */
    private function createCommandByCommandName(string $name): AbstractCommand
    {
        if (!array_key_exists($name, self::COMMAND_CLASSES)) {
            throw new OutOfBoundsException("The command, `{$name}`, does not exist.");
        }

        /** @var class-string<AbstractCommand> */
        $commandClassName = self::COMMAND_CLASSES[$name];

        return new $commandClassName($this);
    }

    /**
     * Exits immediately if the command was not successful.
     *
     * @return self|never
     */
    public function invokeCommand(string $commandName): self
    {
        $exitCode = null;
        $message = null;

        try {
            $command = $this->createCommandByCommandName($commandName);
            $exitCode = $command();
        } catch (CommandFailedException $ex) {
            $exitCode = $ex->getExitCode();
            $message = $ex->getMessage();
        } catch (Throwable $t) {
            $exitCode = AbstractCommand::FAILURE;
            $message = $t->getMessage();
        }

        if (AbstractCommand::SUCCESS !== $exitCode) {
            if ($message) {
                $message = "{$commandName}: {$message}";
            } else {
                $message = "Command `{$commandName}` failed with exit code `{$exitCode}`.";
            }

            $this->failed($message, $exitCode);
        }

        return $this;
    }

    /**
     * @throws RuntimeException If it failed to execute the command.
     */
    public function passthru(string $command): self
    {
        // By default, execute the command at the root of the project.
        /** @var array<string,string> */
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

            /** @phpstan-var bool $successful */

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

    /**
     * @return never
     */
    public function handleRequest(): void
    {
        /** @var int */
        $argc = $this->getRequest()->server['argc'];

        if ($argc < 2) {
            $this->displayHelp();
        }

        /** @var string[] */
        $argv = $this->getRequest()->server['argv'];
        $requestedCommandName = $argv[1];

        $this->invokeCommand($requestedCommandName);

        $this->succeeded($requestedCommandName);
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

    private function getRequest(): HttpRequest
    {
        /** @phpstan-var HttpRequest */
        return $this->getRegistry()->get('request');
    }
}
