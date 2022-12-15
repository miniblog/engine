<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\Registry;
use InvalidArgumentException;
use Miniblog\Engine\Command\AbstractCommand;
use Miniblog\Engine\Command\AssembleDefaultCssCommand;
use Miniblog\Engine\Command\CompileProjectErrorPagesCommand;
use Throwable;

use function implode;
use function is_array;

use const PHP_EOL;
use const null;

class Console
{
    /**
     * @var array<class-string<AbstractCommand>>
     */
    private const COMMAND_CLASSES = [
        CompileProjectErrorPagesCommand::class,
        AssembleDefaultCssCommand::class,
    ];

    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->setRegistry($registry);
    }

    /**
     * @param string|string[] $message
     */
    private function writeLn($message): void
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
        exit(0);
    }

    /**
     * @return never
     */
    private function failed(string $message): void
    {
        $this->writeLn("\033[97;41m[ERROR] {$message}\033[0m");
        // phpcs:ignore
        exit(1);
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

        foreach (self::COMMAND_CLASSES as $commandClassName) {
            $lines[] = '  ' . $commandClassName::COMMAND_NAME;
        }

        $lines[] = '';

        $this->writeLn($lines);

        $this->succeeded();
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
        $requestedCommandName = null;
        $exitCode = null;

        try {
            $requestedCommandName = $argv[1];

            /** @var class-string<AbstractCommand>|null */
            $selectedCommandClassName = null;

            foreach (self::COMMAND_CLASSES as $commandClassName) {
                if ($requestedCommandName === $commandClassName::COMMAND_NAME) {
                    $selectedCommandClassName = $commandClassName;
                    break;
                }
            }

            if (null === $selectedCommandClassName) {
                throw new InvalidArgumentException("The command, `{$requestedCommandName}`, does not exist.");
            }

            $exitCode = (new $selectedCommandClassName($this->getRegistry()))();
        } catch (Throwable $t) {
            $this->failed($t->getMessage());
        }

        if (AbstractCommand::SUCCESS !== $exitCode) {
            $this->failed('The command returned a non-zero exit code.');
        }

        $this->succeeded($requestedCommandName);
    }

    private function setRegistry(Registry $registry): self
    {
        $this->registry = $registry;
        return $this;
    }

    private function getRegistry(): Registry
    {
        return $this->registry;
    }

    private function getRequest(): HttpRequest
    {
        /** @phpstan-var HttpRequest */
        return $this->getRegistry()->get('request');
    }
}
