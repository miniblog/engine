<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use DanBettles\Marigold\OutputHelper\Html5OutputHelper;

class OutputHelperTest extends AbstractTestCase
{
    public function testIsAnHtml5outputhelper(): void
    {
        $this->assertTrue($this->getTestedClass()->isSubclassOf(Html5OutputHelper::class));
    }
}
