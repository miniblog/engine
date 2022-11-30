<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests\Command;

use DanBettles\Marigold\AbstractTestCase;
use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\Registry;
use Miniblog\Engine\Command\AbstractCommand;
use Miniblog\Engine\Command\CompileProjectErrorPagesCommand;
use ReflectionNamedType;

class AbstractCommandTest extends AbstractTestCase
{
    public function testIsAbstract(): void
    {
        $this->assertTrue($this->getTestedClass()->isAbstract());
    }

    public function testIsConstructedFromARegistry(): void
    {
        $registryStub = $this->createStub(Registry::class);

        $command = new class ($registryStub) extends AbstractCommand
        {
            public function __invoke(): int
            {
                return self::SUCCESS;
            }
        };

        $this->assertSame($registryStub, $command->getRegistry());
    }

    public function testIsInvokable(): void
    {
        $invoke = $this->getTestedClass()->getMethod('__invoke');

        $this->assertTrue($invoke->isAbstract());
        $this->assertTrue($invoke->isPublic());

        /** @var ReflectionNamedType */
        $returnType = $invoke->getReturnType();

        $this->assertInstanceOf(ReflectionNamedType::class, $returnType);
        $this->assertSame('int', $returnType->getName());
    }

    public function testGetscriptnameReturnsThePathnameOfTheScript(): void
    {
        $request = HttpRequest::createFromGlobals();
        $request->server['argv'] = ['/path/to/foo', 'bar'];

        $registry = (new Registry())
            ->add('request', $request)
        ;

        $command = new CompileProjectErrorPagesCommand($registry);

        $this->assertSame('/path/to/foo bar', $command->getScriptName());
    }
}
