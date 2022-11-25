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
}
