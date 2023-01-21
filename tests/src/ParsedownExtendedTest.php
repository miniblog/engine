<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use Miniblog\Engine\ParsedownExtended;
use Parsedown;

use function file_get_contents;

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
                <div class="code-block"><code><span class="php__default"></span><span class="php__keyword">(function&nbsp;(</span><span class="php__default">string&nbsp;\$message</span><span class="php__keyword">):&nbsp;</span><span class="php__default">void&nbsp;</span><span class="php__keyword">{<br>&nbsp;&nbsp;&nbsp;&nbsp;echo&nbsp;</span><span class="php__default">\$message</span><span class="php__keyword">;<br>})(</span><span class="php__string">'Hello,&nbsp;World!'</span><span class="php__keyword">);</span></code></div>
                END,
                'containing-fenced-code-block.md',
            ],
            [
                <<<END
                <div class="code-block"><code><span class="php__default">\$domDocument&nbsp;</span><span class="php__keyword">=&nbsp;new&nbsp;</span><span class="php__default">DOMDocument</span><span class="php__keyword">();<br></span><span class="php__default">\$domDocument</span><span class="php__keyword">-&gt;</span><span class="php__default">formatOutput&nbsp;</span><span class="php__keyword">=&nbsp;</span><span class="php__default">false</span><span class="php__keyword">;</span></code></div>
                END,
                'containing-php-with-special-chars.md',
            ],
            [
                <<<END
                <div class="code-block"><code><span class="php__default"></span><span class="php__keyword">echo&nbsp;</span><span class="php__string">'Foo'</span><span class="php__keyword">;</span></code></div>
                <div class="code-block"><code><span class="php__default"></span><span class="php__keyword">echo&nbsp;</span><span class="php__string">'Bar'</span><span class="php__keyword">;</span></code></div>
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
        /** @var string */
        $text = file_get_contents($this->createFixturePathname($fixtureBasename));
        $actualHtml = (new ParsedownExtended())->parse($text);

        $this->assertSame($expectedHtml, $actualHtml);
    }
}
