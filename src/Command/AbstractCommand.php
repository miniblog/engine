<?php

declare(strict_types=1);

namespace Miniblog\Engine\Command;

use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\Registry;

use function array_slice;
use function implode;

abstract class AbstractCommand
{
    /** @var int */
    public const SUCCESS = 0;
    /** @var int */
    public const FAILURE = 1;
    /** @var int */
    public const INVALID = 2;

    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->setRegistry($registry);
    }

    abstract public function __invoke(): int;

    /**
     * Along the lines of `$0` in Bash scripts, the script-name is the command string (e.g.
     * `/path/to/console command-name`) that actually got us to this Command.
     */
    public function getScriptName(): string
    {
        /** @var HttpRequest */
        $request = $this->getRegistry()->get('request');
        /** @var string[] */
        $argv = $request->server['argv'];
        $scriptName = implode(' ', array_slice($argv, 0, 2));

        return $scriptName;
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
}
