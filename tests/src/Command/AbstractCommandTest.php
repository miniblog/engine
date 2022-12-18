<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests\Command;

use DanBettles\Marigold\AbstractTestCase;
use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\Registry;
use Miniblog\Engine\Command\AbstractCommand;
use Miniblog\Engine\Console;
use Miniblog\Engine\Tests\Command\AbstractCommandTest\TestGetscriptnameCommand;
use Miniblog\Engine\Tests\Command\AbstractCommandTest\TestGetCommand;
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
            public function __invoke(): int
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

        $console = new Console($registry);

        require $this->createFixturePathname('TestGetCommand.php');
        $this->assertTrue(is_subclass_of(TestGetCommand::class, AbstractCommand::class));
        $command = new TestGetCommand($console);

        $this->assertSame($dependency, $command->get('dependency'));
    }

    public function testGetscriptnameReturnsThePathnameOfTheScript(): void
    {
        $request = HttpRequest::createFromGlobals();
        $request->server['argv'] = ['/path/to/foo', 'bar'];

        $registry = (new Registry())
            ->add('request', $request)
        ;

        $console = new Console($registry);

        require $this->createFixturePathname('TestGetscriptnameCommand.php');
        $this->assertTrue(is_subclass_of(TestGetscriptnameCommand::class, AbstractCommand::class));
        $command = new TestGetscriptnameCommand($console);

        $this->assertSame('/path/to/foo bar', $command->getScriptName());
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
