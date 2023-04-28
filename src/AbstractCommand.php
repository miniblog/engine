<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use Miniblog\Engine\Console;

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
     * @phpstan-param CommandOptionsArray $options
     */
    abstract public function __invoke(array $options = []): int;

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
