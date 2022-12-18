<?php

declare(strict_types=1);

namespace Miniblog\Engine\Command;

use DanBettles\Marigold\HttpRequest;
use Miniblog\Engine\Console;

use function array_slice;
use function implode;

abstract class AbstractCommand
{
    /** @var int */
    public const SUCCESS = 0;
    /** @var int */
    public const FAILURE = 1;

    private Console $console;

    public function __construct(Console $console)
    {
        $this->setConsole($console);
    }

    /**
     * Along the lines of `$0` in Bash scripts, the script-name is the command string (e.g.
     * `/path/to/console command-name`) that actually got us to this Command.
     */
    public function getScriptName(): string
    {
        /** @var HttpRequest */
        $request = $this->get('request');
        /** @var string[] */
        $argv = $request->server['argv'];
        $scriptName = implode(' ', array_slice($argv, 0, 2));

        return $scriptName;
    }

    abstract public function __invoke(): int;

    private function setConsole(Console $console): self
    {
        $this->console = $console;
        return $this;
    }

    public function getConsole(): Console
    {
        return $this->console;
    }

    /**
     * Returns the service with the specified ID.
     *
     * @return mixed
     */
    public function get(string $id)
    {
        return $this
            ->getConsole()
            ->getRegistry()
            ->get($id)
        ;
    }
}
