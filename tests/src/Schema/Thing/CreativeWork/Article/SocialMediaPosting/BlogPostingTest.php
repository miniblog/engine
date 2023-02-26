<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests\Schema\Thing\CreativeWork\Article\SocialMediaPosting;

use DanBettles\Marigold\AbstractTestCase;
use Miniblog\Engine\Schema\Thing\CreativeWork\Article\SocialMediaPosting;

class BlogPostingTest extends AbstractTestCase
{
    public function testIsASocialmediaposting(): void
    {
        $this->assertTrue($this->getTestedClass()->isSubclassOf(SocialMediaPosting::class));
    }
}
