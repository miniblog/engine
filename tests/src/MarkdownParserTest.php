<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use JsonException;
use Miniblog\Engine\MarkdownParser;
use Parsedown;

use const null;

class MarkdownParserTest extends AbstractTestCase
{
    // Factory method.
    private function createMarkdownParser(): MarkdownParser
    {
        return new MarkdownParser(new Parsedown());
    }

    public function testIsConstructedFromAParsedown(): void
    {
        $parsedown = $this->createStub(Parsedown::class);
        $markdownParser = new MarkdownParser($parsedown);

        $this->assertSame($parsedown, $markdownParser->getParsedown());
    }

    /** @return array<mixed[]> */
    public function providesParsedMarkdownFiles(): array
    {
        return [
            [
                [
                    'body' => null,
                ],
                'empty-article.md',
            ],
            [
                [
                    'body' => <<<END
                    <h1>Title in Markdown</h1>
                    <p>Lorem <strong>ipsum</strong> dolor sit amet.</p>
                    <p>Cras imperdiet ante non tortor iaculis.</p>
                    END,
                ],
                'only-markdown.md',
            ],
            [  // #2
                [
                    'title' => 'Title in Front Matter',
                    'body' => '',
                ],
                'only-front-matter.md',
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
                'front-matter-plus-markdown.md',
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
                'unterminated-front-matter-plus-markdown.md',
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
                'incorrectly-formatted-front-matter-plus-markdown.md',
            ],
        ];
    }

    /**
     * @dataProvider providesParsedMarkdownFiles
     * @param array<string,mixed> $expected
     */
    public function testParseReturnsAnArray(array $expected, string $fileBasename): void
    {
        $parsedMarkdown = $this
            ->createMarkdownParser()
            ->parse($this->getFixtureContents($fileBasename))
        ;

        $this->assertEquals($expected, $parsedMarkdown);
    }

    public function testParseThrowsAnExceptionIfTheFrontMatterJsonIsInvalid(): void
    {
        $this->expectException(JsonException::class);

        $this
            ->createMarkdownParser()
            ->parse($this->getFixtureContents('invalid-front-matter-json-plus-markdown.md'))
        ;
    }

    public function testUsesTheParsedown(): void
    {
        $parsedownMock = $this
            ->getMockBuilder(Parsedown::class)
            ->onlyMethods(['text'])
            ->getMock()
        ;

        $parsedownMock
            ->expects($this->once())
            ->method('text')
            ->with(<<<END
            Lorem ipsum dolor sit amet.

            Cras imperdiet ante non tortor iaculis.

            END)
        ;

        /** @var Parsedown $parsedownMock */
        (new MarkdownParser($parsedownMock))
            ->parse($this->getFixtureContents('front-matter-plus-markdown.md'))
        ;
    }
}
