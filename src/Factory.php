<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\Registry;
use DanBettles\Marigold\Router;
use DanBettles\Marigold\TemplateEngine\Engine;
use DanBettles\Marigold\TemplateEngine\TemplateFileLoader;
use InvalidArgumentException;
use Miniblog\Engine\Action\HomepageAction;
use Miniblog\Engine\Action\ShowBlogPostAction;

use function array_replace;
use function is_dir;
use function dirname;

/**
 * Builds the registry, which wires-up the app's dependencies.
 */
class Factory
{
    private string $projectDir;

    private HttpRequest $request;

    private Registry $registry;

    public function __construct(
        string $projectDir,
        HttpRequest $request
    ) {
        $this
            ->setProjectDir($projectDir)
            ->setRequest($request)
        ;
    }

    private function setProjectDir(string $projectDir): self
    {
        if (!is_dir($projectDir)) {
            throw new InvalidArgumentException("The project directory, `{$projectDir}`, does not exist.");
        }

        $this->projectDir = $projectDir;

        return $this;
    }

    public function getProjectDir(): string
    {
        return $this->projectDir;
    }

    private function setRequest(HttpRequest $request): self
    {
        $this->request = $request;
        return $this;
    }

    public function getRequest(): HttpRequest
    {
        return $this->request;
    }

    /**
     * Strictly private.
     *
     * @return array<string,mixed>
     * @todo Create an object!
     */
    private function createAugmentedConfig(): array
    {
        $engineDir = dirname(__DIR__);
        $projectDir = $this->getProjectDir();

        return array_replace(require "{$projectDir}/config.php", [
            'engineDir' => $engineDir,
            'engineTemplatesDir' => "{$engineDir}/templates",
            'projectDir' => $projectDir,
            'projectTemplatesDir' => "{$projectDir}/templates",
        ]);
    }

    /**
     * Strictly private.
     */
    private function createRouter(): Router
    {
        return new Router([
            [
                'id' => 'homepage',
                'path' => '/',
                'action' => HomepageAction::class,
            ],
            [
                'id' => 'showBlogPost',
                'path' => '/blog/{postId}',
                'action' => ShowBlogPostAction::class,
            ],
        ]);
    }

    /**
     * Strictly private.
     */
    private function createTemplateFileLoader(Registry $registry): TemplateFileLoader
    {
        /** @var array<string,string> */
        $config = $registry->get('config');

        return new TemplateFileLoader([
            'Overrides' => "{$config['projectTemplatesDir']}",
            'Default templates' => "{$config['engineTemplatesDir']}",
        ]);
    }

    /**
     * Strictly private.
     */
    private function createTemplateEngine(Registry $registry): Engine
    {
        /** @var TemplateFileLoader */
        $templateFileLoader = $registry->get('templateFileLoader');

        return Engine::create($templateFileLoader, $registry);
    }

    /**
     * Strictly private.
     */
    private function createOutputHelper(Registry $registry): OutputHelper
    {
        /** @var Router */
        $router = $registry->get('router');

        return new OutputHelper($router);
    }

    /**
     * Strictly private.
     */
    private function createArticleManager(Registry $registry): ArticleManager
    {
        /** @var array<string,string> */
        $config = $registry->get('config');

        return new ArticleManager(
            new MarkdownParser(new ParsedownExtended()),
            "{$config['projectDir']}/content"
        );
    }

    /**
     * Strictly private.
     */
    private function createErrorsService(Registry $registry): ErrorsService
    {
        /** @var array<string,string> */
        $config = $registry->get('config');

        return new ErrorsService($config);
    }

    public function getRegistry(): Registry
    {
        if (!isset($this->registry)) {
            $this->registry = (new Registry())
                ->add('config', $this->createAugmentedConfig())
                ->add('request', $this->getRequest())

                ->addFactory('router', function (): Router {
                    return $this->createRouter();
                })
                ->addFactory('templateFileLoader', function (Registry $registry): TemplateFileLoader {
                    return $this->createTemplateFileLoader($registry);
                })
                ->addFactory('templateEngine', function (Registry $registry): Engine {
                    return $this->createTemplateEngine($registry);
                })
                ->addFactory('outputHelper', function (Registry $registry): OutputHelper {
                    return $this->createOutputHelper($registry);
                })
                ->addFactory('articleManager', function (Registry $registry): ArticleManager {
                    return $this->createArticleManager($registry);
                })
                ->addFactory('errorsService', function (Registry $registry): ErrorsService {
                    return $this->createErrorsService($registry);
                })
            ;
        }

        return $this->registry;
    }
}
