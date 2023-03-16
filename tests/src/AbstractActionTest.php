<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractAction as MarigoldAbstractAction;
use DanBettles\Marigold\AbstractTestCase;
use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\HttpResponse;
use DanBettles\Marigold\HttpResponse\RedirectHttpResponse;
use DanBettles\Marigold\Registry;
use DanBettles\Marigold\Router;
use DanBettles\Marigold\TemplateEngine\Engine;
use Miniblog\Engine\AbstractAction;
use Miniblog\Engine\Tests\AbstractActionTest\RendersDefaultTemplateAction;

use function is_subclass_of;

use const null;

class AbstractActionTest extends AbstractTestCase
{
    public function testIsAMarigoldAction(): void
    {
        $this->assertTrue($this->getTestedClass()->isSubclassOf(MarigoldAbstractAction::class));
    }

    public function testIsAbstract(): void
    {
        $this->assertTrue($this->getTestedClass()->isAbstract());
    }

    public function testConstructorAlsoAcceptsServices(): void
    {
        $templateEngineStub = $this->createStub(Engine::class);
        $servicesStub = $this->createStub(Registry::class);

        $action = new class ($templateEngineStub, $servicesStub) extends AbstractAction
        {
            public function __invoke(HttpRequest $request): HttpResponse
            {
                return new HttpResponse();
            }
        };

        $this->assertSame($servicesStub, $action->getServices());
    }

    public function testRenderdefaultRendersTheDefaultTemplateForTheAction(): void
    {
        $mockClassName = 'RendersDefaultTemplateActionMock';
        $expectedResponse = new HttpResponse('404 Not Found', 404);

        $this->assertTrue(is_subclass_of(RendersDefaultTemplateAction::class, AbstractAction::class));

        $actionMock = $this
            ->getMockBuilder(RendersDefaultTemplateAction::class)
            ->setMockClassName($mockClassName)
            ->onlyMethods(['render'])
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $actionMock
            ->expects($this->once())
            ->method('render')
            ->with("{$mockClassName}/default.html.php", [
                'message' => '404 Not Found',
            ], 404)
            ->willReturn($expectedResponse)
        ;

        /** @var AbstractAction $actionMock */
        $actualResponse = $actionMock(HttpRequest::createFromGlobals());

        $this->assertSame($expectedResponse, $actualResponse);
    }

    public function testRedirecttorouteCreatesARedirecthttpresponse(): void
    {
        $templateEngineStub = $this->createStub(Engine::class);

        $routerMock = $this
            ->getMockBuilder(Router::class)
            ->onlyMethods(['generatePath'])
            ->setConstructorArgs([
                [
                    [
                        'id' => 'showHomepage',
                        'path' => '/',
                        'action' => null,
                    ],
                ],
            ])
            ->getMock()
        ;

        $routerMock
            ->expects($this->once())
            ->method('generatePath')
            ->with('showHomepage')
            ->willReturn('/')
        ;

        $servicesMock = $this
            ->getMockBuilder(Registry::class)
            ->onlyMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $servicesMock
            ->expects($this->once())
            ->method('get')
            ->with('router')
            ->willReturn($routerMock)
        ;

        $action = new class ($templateEngineStub, $servicesMock) extends AbstractAction
        {
            public function __invoke(HttpRequest $request): HttpResponse
            {
                return $this->redirectToRoute('showHomepage');
            }
        };

        /** @var RedirectHttpResponse */
        $actualResponse = $action(HttpRequest::createFromGlobals());

        $this->assertInstanceOf(RedirectHttpResponse::class, $actualResponse);
        $this->assertSame(302, $actualResponse->getStatusCode());
        $this->assertSame('/', $actualResponse->getTargetUrl());
    }

    public function testRedirecttorouteAcceptsRouteParameters(): void
    {
        $templateEngineStub = $this->createStub(Engine::class);

        $routerMock = $this
            ->getMockBuilder(Router::class)
            ->onlyMethods(['generatePath'])
            ->setConstructorArgs([
                [
                    [
                        'id' => 'showArticle',
                        'path' => '/articles/{id}',
                        'action' => null,
                    ],
                ],
            ])
            ->getMock()
        ;

        $routerMock
            ->expects($this->once())
            ->method('generatePath')
            ->with('showArticle', ['id' => 123])
            ->willReturn('/articles/123')
        ;

        $servicesMock = $this
            ->getMockBuilder(Registry::class)
            ->onlyMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $servicesMock
            ->expects($this->once())
            ->method('get')
            ->with('router')
            ->willReturn($routerMock)
        ;

        $action = new class ($templateEngineStub, $servicesMock) extends AbstractAction
        {
            public function __invoke(HttpRequest $request): HttpResponse
            {
                return $this->redirectToRoute('showArticle', ['id' => 123]);
            }
        };

        /** @var RedirectHttpResponse */
        $actualResponse = $action(HttpRequest::createFromGlobals());

        $this->assertInstanceOf(RedirectHttpResponse::class, $actualResponse);
        $this->assertSame(302, $actualResponse->getStatusCode());
        $this->assertSame('/articles/123', $actualResponse->getTargetUrl());
    }

    public function testRedirecttorouteAcceptsAnAlternativeStatusCode(): void
    {
        $templateEngineStub = $this->createStub(Engine::class);

        $routerMock = $this
            ->getMockBuilder(Router::class)
            ->onlyMethods(['generatePath'])
            ->setConstructorArgs([
                [
                    [
                        'id' => 'showArticles',
                        'path' => '/articles',
                        'action' => null,
                    ],
                ],
            ])
            ->getMock()
        ;

        $routerMock
            ->expects($this->once())
            ->method('generatePath')
            ->with('showArticles', [])
            ->willReturn('/articles')
        ;

        $servicesMock = $this
            ->getMockBuilder(Registry::class)
            ->onlyMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $servicesMock
            ->expects($this->once())
            ->method('get')
            ->with('router')
            ->willReturn($routerMock)
        ;

        $action = new class ($templateEngineStub, $servicesMock) extends AbstractAction
        {
            public function __invoke(HttpRequest $request): HttpResponse
            {
                return $this->redirectToRoute('showArticles', [], 303);
            }
        };

        /** @var RedirectHttpResponse */
        $actualResponse = $action(HttpRequest::createFromGlobals());

        $this->assertInstanceOf(RedirectHttpResponse::class, $actualResponse);
        $this->assertSame(303, $actualResponse->getStatusCode());
        $this->assertSame('/articles', $actualResponse->getTargetUrl());
    }
}
