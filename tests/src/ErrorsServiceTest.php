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
            "show_error_action/error_404.html.php",
            $service->createRenderPathname(404)
        );
    }

    public function testGetpagepathnamesReturnsAnArrayOfThePathnamesOfErrorPages(): void
    {
        $varDir = $this->createFixturePathname(__FUNCTION__);

        $service = new ErrorsService([
            'varDir' => $varDir,
        ]);

        $this->assertSame([
            404 => "{$varDir}/show_error_action/error_404.html",
            500 => "{$varDir}/show_error_action/error_500.html",
        ], $service->getPagePathnames());
    }

    public function testGetpagepathnameReturnsThePathnameOfTheErrorPageForAStatusCode(): void
    {
        $varDir = $this->createFixturePathname(__FUNCTION__);

        $service = new ErrorsService([
            'varDir' => $varDir,
        ]);

        $this->assertSame("{$varDir}/show_error_action/error_404.html", $service->getPagePathname(404));
    }
}
