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
use Miniblog\Engine\Action\SignUpAction;
use Miniblog\Engine\Action\ShowBlogPostAction;
use Miniblog\Engine\Action\ShowSignUpCompleteAction;
use Miniblog\Engine\Action\ShowSignUpConfirmationEmailAction;
use Miniblog\Engine\Action\ShowSignUpPendingAction;
use Miniblog\Engine\Action\AddSubscriberAction;
use Miniblog\Engine\Schema\Thing;
use ReflectionClass;

use function is_dir;
use function dirname;

/**
 * Builds the registry, which wires-up the app's dependencies.
 */
class Factory
{
    private string $projectDir;

    private string $env;

    private HttpRequest $request;

    private Registry $registry;

    public function __construct(
        string $projectDir,
        string $env,
        HttpRequest $request
    ) {
        $this
            ->setProjectDir($projectDir)
            ->setEnv($env)
            ->setRequest($request)
        ;
    }

    /**
     * Strictly private.
     *
     * @phpstan-return ConfigArray
     * @todo Create an object!
     */
    private function createAugmentedConfig(): array
    {
        $engineDir = dirname(__DIR__);
        $projectDir = $this->getProjectDir();

        return [
            'env' => $this->getEnv(),
            'engineDir' => $engineDir,
            'engineTemplatesDir' => "{$engineDir}/templates",
            'projectDir' => $projectDir,
            'projectTemplatesDir' => "{$projectDir}/templates",
            'dataDir' => "{$projectDir}/data",
        ];
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
                'id' => 'showBlogPosting',
                'path' => '/blog/{postingId}',
                'action' => ShowBlogPostAction::class,
            ],
            [
                'id' => 'signUp',
                'path' => '/sign-up',
                'action' => SignUpAction::class,
            ],
            [
                'id' => 'showSignUpConfirmationEmail',
                'path' => '/sign-up/confirmation-email',
                'action' => ShowSignUpConfirmationEmailAction::class,
            ],
            [
                'id' => 'showSignUpPending',
                'path' => '/sign-up/pending',
                'action' => ShowSignUpPendingAction::class,
            ],
            [
                'id' => 'showSignUpCompleteAction',
                'path' => '/sign-up/complete',
                'action' => ShowSignUpCompleteAction::class,
            ],
            [
                'id' => 'addSubscriberAction',
                'path' => '/subscribers',
                'action' => AddSubscriberAction::class,
            ],
        ]);
    }

    /**
     * Strictly private.
     */
    private function createTemplateFileLoader(Registry $registry): TemplateFileLoader
    {
        /** @phpstan-var ConfigArray */
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
    private function createThingManager(Registry $registry): ThingManager
    {
        $baseThingClass = new ReflectionClass(Thing::class);
        /** @phpstan-var ConfigArray */
        $config = $registry->get('config');

        return new ThingManager(
            $baseThingClass->getNamespaceName(),
            $config['dataDir'],
            new DocumentParser(new ParsedownExtended())
        );
    }

    /**
     * Strictly private.
     */
    private function createErrorsService(Registry $registry): ErrorsService
    {
        /** @phpstan-var ConfigArray */
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
                ->addFactory('thingManager', function (Registry $registry): ThingManager {
                    return $this->createThingManager($registry);
                })
                ->addFactory('errorsService', function (Registry $registry): ErrorsService {
                    return $this->createErrorsService($registry);
                })
            ;
        }

        return $this->registry;
    }

    /**
     * @throws InvalidArgumentException If the directory does not exist
     */
    private function setProjectDir(string $dir): self
    {
        if (!is_dir($dir)) {
            throw new InvalidArgumentException("The project directory, `{$dir}`, does not exist");
        }

        $this->projectDir = $dir;

        return $this;
    }

    public function getProjectDir(): string
    {
        return $this->projectDir;
    }

    private function setEnv(string $env): self
    {
        $this->env = $env;
        return $this;
    }

    public function getEnv(): string
    {
        return $this->env;
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
}
