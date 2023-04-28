<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use DanBettles\Marigold\Registry;
use Miniblog\Engine\AbstractCommand;
use Miniblog\Engine\Console;
use Miniblog\Engine\Tests\AbstractCommandTest\TestGetCommand;
use ReflectionNamedType;
use stdClass;

use function is_subclass_of;

class AbstractCommandTest extends AbstractTestCase
{
    public function testIsAbstract(): void
    {
        $this->assertTrue($this->getTestedClass()->isAbstract());
    }

    public function testIsConstructedFromAConsole(): void
    {
        $consoleStub = $this->createStub(Console::class);

        $command = new class ($consoleStub) extends AbstractCommand
        {
            public function __invoke(array $options = []): int
            {
                return self::SUCCESS;
            }
        };

        $this->assertSame($consoleStub, $command->getConsole());
    }

    public function testGetReturnsADependency(): void
    {
        $dependency = new stdClass();

        $registry = (new Registry())
            ->add('dependency', $dependency)
        ;

        $console = new Console($registry, []);

        $this->assertTrue(is_subclass_of(TestGetCommand::class, AbstractCommand::class));
        $command = new TestGetCommand($console);

        $this->assertSame($dependency, $command->get('dependency'));
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
}
