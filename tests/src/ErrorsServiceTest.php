<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use Miniblog\Engine\ErrorsService;

class ErrorsServiceTest extends AbstractTestCase
{
    public function testIsInstantiable(): void
    {
        $config = ['foo' => 'bar'];
        $service = new ErrorsService($config);

        $this->assertSame($config, $service->getConfig());
    }

    public function testCreaterenderpathnameReturnsTheRenderPathnameOfAnErrorTemplate(): void
    {
        $service = new ErrorsService([]);

        $this->assertSame(
            "ShowErrorAction/error_404.html.php",
            $service->createRenderPathname(404)
        );
    }

    public function testGetpagepathnamesReturnsAnArrayOfThePathnamesOfErrorPages(): void
    {
        $projectDir = $this->createFixturePathname(__FUNCTION__);

        $service = new ErrorsService([
            'projectDir' => $projectDir,
        ]);

        $this->assertSame([
            404 => "{$projectDir}/public/errors/error-404.html",
            500 => "{$projectDir}/public/errors/error-500.html",
        ], $service->getPagePathnames());
    }

    public function testGetpagepathnameReturnsThePathnameOfTheErrorPageForAStatusCode(): void
    {
        $projectDir = $this->createFixturePathname(__FUNCTION__);

        $service = new ErrorsService([
            'projectDir' => $projectDir,
        ]);

        $this->assertSame("{$projectDir}/public/errors/error-404.html", $service->getPagePathname(404));
    }

    public function testGetpagedirReturnsThePathnameOfThePageDirectory(): void
    {
        $projectDir = $this->createFixturePathname(__FUNCTION__);

        $service = new ErrorsService([
            'projectDir' => $projectDir,
        ]);

        $this->assertSame("{$projectDir}/public/errors", $service->getPageDir());
    }
}
