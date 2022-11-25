<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests\Action;

use DanBettles\Marigold\AbstractTestCase;
use Miniblog\Engine\Action\AbstractAction;

class ShowBlogPostActionTest extends AbstractTestCase
{
    public function testIsAMiniblogAction(): void
    {
        $this->assertTrue($this->getTestedClass()->isSubclassOf(AbstractAction::class));
    }
}
