<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use Miniblog\Engine\ArticleManager;
use Miniblog\Engine\FrontController;
use Miniblog\Engine\MarkdownParser;
use ReflectionClass;

use function array_replace;
use function dirname;

/**
 * Facade.  Helps keep code out of `index.php`, which won't be under our control in a blog project.
 */
class Miniblog
{
    private string $projectDir;

    /**
     * @var array<string, mixed>
     */
    private array $defaultConfig;

    /**
     * @param array<string, mixed> $defaultConfig
     */
    public function __construct(
        string $projectDir,
        array $defaultConfig
    ) {
        $this->projectDir = $projectDir;
        $this->defaultConfig = $defaultConfig;
    }

    /**
     * @param array<string, string> $queryVars
     * @param array<string, string> $requestVars
     * @param array<string, string> $serverVars
     */
    public function run(
        array $queryVars,
        array $requestVars,
        array $serverVars
    ): void {
        $frontControllerClass = new ReflectionClass(FrontController::class);

        /** @var string */
        $classPathName = $frontControllerClass->getFileName();
        $engineDir = dirname(dirname($classPathName));

        /** @var array<string, mixed> */
        $config = array_replace($this->defaultConfig, [
            'projectDir' => $this->projectDir,
            'engineDir' => $engineDir,
        ]);

        $articleManager = new ArticleManager(new MarkdownParser(), "{$this->projectDir}/content");

        $response = $frontControllerClass
            ->newInstance($config, $articleManager)
            ->handle($serverVars)
        ;

        $response->send($serverVars);
    }
}
