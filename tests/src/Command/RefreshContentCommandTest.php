<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests\Command;

use DanBettles\Marigold\AbstractTestCase;
use Miniblog\Engine\AbstractCommand;

class RefreshContentCommandTest extends AbstractTestCase
{
    public function testIsAnAbstractcommand(): void
    {
        $this->assertTrue($this->getTestedClass()->isSubclassOf(AbstractCommand::class));
    }
}
