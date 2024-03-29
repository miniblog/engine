<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use Miniblog\Engine\ParsedownExtended;
use Parsedown;

class ParsedownExtendedTest extends AbstractTestCase
{
    public function testIsAParsedown(): void
    {
        $this->assertTrue($this->getTestedClass()->isSubclassOf(Parsedown::class));
    }

    /** @return array<mixed[]> */
    public function providesHighlightedPhp(): array
    {
        return [
            [
                <<<END
                <div class="code-block"><code><span class="code__default"></span><span class="code__keyword">(function&nbsp;(</span><span class="code__default">string&nbsp;\$message</span><span class="code__keyword">):&nbsp;</span><span class="code__default">void&nbsp;</span><span class="code__keyword">{<br>&nbsp;&nbsp;&nbsp;&nbsp;echo&nbsp;</span><span class="code__default">\$message</span><span class="code__keyword">;<br>})(</span><span class="code__string">'Hello,&nbsp;World!'</span><span class="code__keyword">);</span></code></div>
                END,
                'containing-fenced-code-block.md',
            ],
            [
                <<<END
                <div class="code-block"><code><span class="code__default">\$domDocument&nbsp;</span><span class="code__keyword">=&nbsp;new&nbsp;</span><span class="code__default">DOMDocument</span><span class="code__keyword">();<br></span><span class="code__default">\$domDocument</span><span class="code__keyword">-&gt;</span><span class="code__default">formatOutput&nbsp;</span><span class="code__keyword">=&nbsp;</span><span class="code__default">false</span><span class="code__keyword">;</span></code></div>
                END,
                'containing-php-with-special-chars.md',
            ],
            [
                <<<END
                <div class="code-block"><code><span class="code__default"></span><span class="code__keyword">echo&nbsp;</span><span class="code__string">'Foo'</span><span class="code__keyword">;</span></code></div>
                <div class="code-block"><code><span class="code__default"></span><span class="code__keyword">echo&nbsp;</span><span class="code__string">'Bar'</span><span class="code__keyword">;</span></code></div>
                END,
                'containing-multiple-fenced-code-blocks.md',
            ],
        ];
    }

    /** @dataProvider providesHighlightedPhp */
    public function testHighlightsPhpInFencedCodeBlocks(
        string $expectedHtml,
        string $fixtureBasename
    ): void {
        $actualHtml = (new ParsedownExtended())
            ->text($this->getFixtureContents($fixtureBasename))
        ;

        $this->assertSame($expectedHtml, $actualHtml);
    }
}
