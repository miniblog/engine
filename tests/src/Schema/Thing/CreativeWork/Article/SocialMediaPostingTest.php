<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests\Schema\Thing\CreativeWork\Article;

use DanBettles\Marigold\AbstractTestCase;
use Miniblog\Engine\Schema\Thing\CreativeWork\Article;

class SocialMediaPostingTest extends AbstractTestCase
{
    public function testIsAnArticle(): void
    {
        $this->assertTrue($this->getTestedClass()->isSubclassOf(Article::class));
    }
}
