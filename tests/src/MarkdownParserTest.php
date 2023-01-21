<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use JsonException;
use Miniblog\Engine\MarkdownParser;
use Parsedown;

use function file_get_contents;

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
        $parsedMarkdown = $this->createMarkdownParser()->parse($text);

        $this->assertEquals($expected, $parsedMarkdown);
    }

    public function testParseThrowsAnExceptionIfTheFrontMatterJsonIsInvalid(): void
    {
        $this->expectException(JsonException::class);

        /** @var string */
        $text = file_get_contents($this->createFixturePathname('invalid-front-matter-json-plus-markdown.md'));
        $this->createMarkdownParser()->parse($text);
    }

    public function testUsesTheParsedown(): void
    {
        /** @var string */
        $text = file_get_contents($this->createFixturePathname('front-matter-plus-markdown.md'));

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
        (new MarkdownParser($parsedownMock))->parse($text);
    }
}
