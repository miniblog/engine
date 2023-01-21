<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\Registry;
use DanBettles\Marigold\Router;
use DanBettles\Marigold\TemplateEngine\Engine;
use DanBettles\Marigold\TemplateEngine\TemplateFileLoader;
use InvalidArgumentException;
use Miniblog\Engine\ArticleManager;
use Miniblog\Engine\ErrorsService;
use Miniblog\Engine\Factory;
use Miniblog\Engine\OutputHelper;
use Miniblog\Engine\ParsedownExtended;

use function dirname;

class FactoryTest extends AbstractTestCase
{
    // Factory method.
    private function createFactory(string $projectDir): Factory
    {
        return new Factory($projectDir, HttpRequest::createFromGlobals());
    }

    public function testIsInstantiable(): void
    {
        $projectDir = $this->createFixturePathname(__FUNCTION__);
        $request = HttpRequest::createFromGlobals();
        $factory = new Factory($projectDir, $request);

        $this->assertSame($projectDir, $factory->getProjectDir());
        $this->assertSame($request, $factory->getRequest());
    }

    public function testThrowsAnExceptionIfTheProjectDirDoesNotExist(): void
    {
        $fixturesDir = $this->createFixturePathname(__FUNCTION__);
        $nonExistentDir = "{$fixturesDir}/non_existent";

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The project directory, `{$nonExistentDir}`, does not exist.");

        $this->createFactory($nonExistentDir);
    }

    public function testGetregistryCreatesTheRegistry(): void
    {
        $projectDir = $this->createFixturePathname(__FUNCTION__);
        $engineDir = dirname(dirname(__DIR__));
        $request = HttpRequest::createFromGlobals();

        $registry = (new Factory($projectDir, $request))->getRegistry();

        $this->assertInstanceOf(Registry::class, $registry);

        $config = $registry->get('config');

        $this->assertSame([
            'site' => [],
            'owner' => [],
            'env' => 'prod',
            'engineDir' => $engineDir,
            'engineTemplatesDir' => "{$engineDir}/templates",
            'projectDir' => $projectDir,
            'projectTemplatesDir' => "{$projectDir}/templates",
            'varDir' => "{$projectDir}/var",
        ], $config);

        $requestFromRegistry = $registry->get('request');

        $this->assertSame($request, $requestFromRegistry);

        /** @var Router */
        $router = $registry->get('router');

        $this->assertInstanceOf(Router::class, $router);
        $this->assertCount(2, $router->getRoutes());

        /** @var TemplateFileLoader */
        $templateFileLoader = $registry->get('templateFileLoader');

        $this->assertInstanceOf(TemplateFileLoader::class, $templateFileLoader);
        $this->assertCount(2, $templateFileLoader->getTemplateDirs());

        /** @var Engine */
        $templateEngine = $registry->get('templateEngine');

        $this->assertInstanceOf(Engine::class, $templateEngine);
        $this->assertSame($templateFileLoader, $templateEngine->getTemplateFileLoader());
        $this->assertSame($registry, $templateEngine->getGlobals());

        /** @var OutputHelper */
        $outputHelper = $registry->get('outputHelper');

        $this->assertInstanceOf(OutputHelper::class, $outputHelper);
        $this->assertSame($router, $outputHelper->getRouter());

        /** @var ArticleManager */
        $articleManager = $registry->get('articleManager');

        $this->assertInstanceOf(ArticleManager::class, $articleManager);
        $this->assertInstanceOf(ParsedownExtended::class, $articleManager->getMarkdownParser()->getParsedown());
        $this->assertSame("{$projectDir}/content", $articleManager->getDataDir());

        /** @var ErrorsService */
        $errorsService = $registry->get('errorsService');

        $this->assertInstanceOf(ErrorsService::class, $errorsService);
        $this->assertSame($config, $errorsService->getConfig());
    }

    public function testGetregistryAlwaysReturnsTheSameInstance(): void
    {
        $factory = $this->createFactory($this->createFixturePathname(__FUNCTION__));

        $registry = $factory->getRegistry();

        $this->assertSame($registry, $factory->getRegistry());
    }
}
