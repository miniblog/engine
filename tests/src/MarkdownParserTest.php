<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use JsonException;
use Miniblog\Engine\MarkdownParser;

use function file_get_contents;

use const null;

class MarkdownParserTest extends AbstractTestCase
{
    /** @return array<mixed[]> */
    public function providesParsedMarkdownFiles(): array
    {
        return [
            [
                [
                    'body' => null,
                ],
                $this->createFixturePathname('empty-article.md'),
            ],
            [
                [
                    'body' => <<<END
                    <h1>Title in Markdown</h1>
                    <p>Lorem <strong>ipsum</strong> dolor sit amet.</p>
                    <p>Cras imperdiet ante non tortor iaculis.</p>
                    END,
                ],
                $this->createFixturePathname('only-markdown.md'),
            ],
            [  // #2
                [
                    'title' => 'Title in Front Matter',
                    'body' => '',
                ],
                $this->createFixturePathname('only-front-matter.md'),
            ],
            [
                [
                    'title' => 'Title in Front Matter',
                    'description' => 'Description in front matter.',
                    'body' => <<<END
                    <p>Lorem ipsum dolor sit amet.</p>
                    <p>Cras imperdiet ante non tortor iaculis.</p>
                    END,
                    'publishedAt' => '2022-08-26',  // From front matter.
                ],
                $this->createFixturePathname('front-matter-plus-markdown.md'),
            ],
            [
                [
                    'body' => <<<END
                    <p>{
                    &quot;title&quot;: &quot;Title in Front Matter&quot;</p>
                    <p>Lorem ipsum dolor sit amet.</p>
                    <p>Cras imperdiet ante non tortor iaculis.</p>
                    END,
                ],
                $this->createFixturePathname('unterminated-front-matter-plus-markdown.md'),
            ],
            [  // #5
                [
                    'body' => <<<END
                    <p>{
                    &quot;title&quot;: &quot;Title in Front Matter&quot;
                    }
                    Lorem ipsum dolor sit amet.</p>
                    <p>Cras imperdiet ante non tortor iaculis.</p>
                    END,
                ],
                $this->createFixturePathname('incorrectly-formatted-front-matter-plus-markdown.md'),
            ],
        ];
    }

    /**
     * @dataProvider providesParsedMarkdownFiles
     * @param array<string,mixed> $expected
     */
    public function testParseReturnsAnArray(array $expected, string $filePathname): void
    {
        /** @var string */
        $text = file_get_contents($filePathname);
        $parsedMarkdown = (new MarkdownParser())->parse($text);

        $this->assertEquals($expected, $parsedMarkdown);
    }

    public function testParseThrowsAnExceptionIfTheFrontMatterJsonIsInvalid(): void
    {
        $this->expectException(JsonException::class);

        /** @var string */
        $text = file_get_contents($this->createFixturePathname('invalid-front-matter-json-plus-markdown.md'));
        (new MarkdownParser())->parse($text);
    }

    public function testHighlightsPhpInFencedCodeBlocks(): void
    {
        /** @var string */
        $text = file_get_contents($this->createFixturePathname('containing-fenced-code-block.md'));
        $parsedMarkdown = (new MarkdownParser())->parse($text);

        $this->assertSame(<<<END
        <div class="code-block"><code><span class="php__default"></span><span class="php__keyword">(function&nbsp;(</span><span class="php__default">string&nbsp;\$message</span><span class="php__keyword">):&nbsp;</span><span class="php__default">void&nbsp;</span><span class="php__keyword">{<br>&nbsp;&nbsp;&nbsp;&nbsp;echo&nbsp;</span><span class="php__default">\$message</span><span class="php__keyword">;<br>})(</span><span class="php__string">'Hello,&nbsp;World!'</span><span class="php__keyword">);</span></code></div>
        END, $parsedMarkdown['body']);
    }
}
