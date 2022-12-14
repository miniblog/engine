<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests\Action;

use DanBettles\Marigold\AbstractAction as MarigoldAbstractAction;
use DanBettles\Marigold\AbstractTestCase;
use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\HttpResponse;
use DanBettles\Marigold\Registry;
use DanBettles\Marigold\TemplateEngine\Engine;
use Miniblog\Engine\Action\AbstractAction;
use Miniblog\Engine\Tests\Action\AbstractActionTest\RendersDefaultTemplateAction;

use function is_subclass_of;

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
        $expectedTemplateDirBasename = 'renders_default_template_action_mock';
        $expectedResponse = new HttpResponse('404 Not Found', HttpResponse::HTTP_NOT_FOUND);

        require $this->createFixturePathname('RendersDefaultTemplateAction.php');
        $this->assertTrue(is_subclass_of(RendersDefaultTemplateAction::class, AbstractAction::class));

        $actionMock = $this
            ->getMockBuilder(RendersDefaultTemplateAction::class)
            ->setMockClassName('RendersDefaultTemplateActionMock')
            ->onlyMethods(['render'])
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $actionMock
            ->expects($this->once())
            ->method('render')
            ->with("{$expectedTemplateDirBasename}/default.html.php", [
                'message' => '404 Not Found',
            ], HttpResponse::HTTP_NOT_FOUND)
            ->willReturn($expectedResponse)
        ;

        /** @var AbstractAction $actionMock */
        $actualResponse = $actionMock(HttpRequest::createFromGlobals());

        $this->assertSame($expectedResponse, $actualResponse);
    }
}
