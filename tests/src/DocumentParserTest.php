<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use JsonException;
use Miniblog\Engine\DocumentParser;
use Parsedown;

use const null;

class DocumentParserTest extends AbstractTestCase
{
    // Factory method.
    private function createDocumentParser(): DocumentParser
    {
        return new DocumentParser(new Parsedown());
    }

    public function testIsConstructedFromAParsedown(): void
    {
        $parsedown = $this->createStub(Parsedown::class);
        $documentParser = new DocumentParser($parsedown);

        $this->assertSame($parsedown, $documentParser->getParsedown());
    }

    /** @return array<mixed[]> */
    public function providesParsedMarkdownFiles(): array
    {
        return [
            [
                [
                    'frontMatter' => [],
                    'body' => null,
                ],
                'empty-article.md',
            ],
            [
                [
                    'frontMatter' => [],
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
                    'frontMatter' => [
                        'title' => 'Title in Front Matter',
                    ],
                    'body' => null,
                ],
                'only-front-matter.md',
            ],
            [
                [
                    'frontMatter' => [
                        'title' => 'Title in Front Matter',
                        'description' => 'Description in front matter.',
                        'publishedAt' => '2022-08-26',
                    ],
                    'body' => <<<END
                    <p>Lorem ipsum dolor sit amet.</p>
                    <p>Cras imperdiet ante non tortor iaculis.</p>
                    END,
                ],
                'front-matter-plus-markdown.md',
            ],
            [
                [
                    'frontMatter' => [],
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
                    'frontMatter' => [],
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
            ->createDocumentParser()
            ->parse($this->getFixtureContents($fileBasename))
        ;

        $this->assertSame($expected, $parsedMarkdown);
    }

    public function testParseThrowsAnExceptionIfTheFrontMatterJsonIsInvalid(): void
    {
        $this->expectException(JsonException::class);

        $this
            ->createDocumentParser()
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
        (new DocumentParser($parsedownMock))
            ->parse($this->getFixtureContents('front-matter-plus-markdown.md'))
        ;
    }
}
