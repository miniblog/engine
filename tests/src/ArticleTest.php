<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use DateTime;
use Miniblog\Engine\Article;

use const false;
use const null;
use const true;

class ArticleTest extends AbstractTestCase
{
    public function testIsInstantiable(): void
    {
        $article = new Article();

        $this->assertNull($article->getId());
        $this->assertNull($article->getTitle());
        $this->assertNull($article->getDescription());
        $this->assertNull($article->getBody());
        $this->assertNull($article->getPublishedAt());
        $this->assertFalse($article->isLegacyArticle());
    }

    public function testHasAccessorsForAllProperties(): void
    {
        $article = new Article();

        $foo = $article->setId('foo-bar');
        $this->assertSame('foo-bar', $article->getId());
        $this->assertSame($article, $foo);

        $bar = $article->setTitle('New Title');
        $this->assertSame('New Title', $article->getTitle());
        $this->assertSame($article, $bar);

        $baz = $article->setDescription('New description');
        $this->assertSame('New description', $article->getDescription());
        $this->assertSame($article, $baz);

        $qux = $article->setBody('New body');
        $this->assertSame('New body', $article->getBody());
        $this->assertSame($article, $qux);

        // From `DateTime`.
        $quux = $article->setPublishedAt(new DateTime('1969-07-16'));
        $this->assertEquals(new DateTime('1969-07-16'), $article->getPublishedAt());
        $this->assertSame($article, $quux);
        // From date/time string.
        $article->setPublishedAt('1987-10-15');
        $this->assertEquals(new DateTime('1987-10-15'), $article->getPublishedAt());

        $corge = $article->setIsLegacyArticle(true);
        $this->assertTrue($article->isLegacyArticle());
        $this->assertSame($article, $corge);
    }

    /** @return array<int, array<int, mixed>> */
    public function providesArticlesCreatedFromArrays(): array
    {
        $dateStr = '2022-08-27';

        return [
            [
                (new Article())
                    ->setId('123')
                    ->setTitle('Title')
                    ->setDescription(null)
                    ->setBody('Body')
                    ->setPublishedAt(null)
                    ->setIsLegacyArticle(false),
                // Some:
                [
                    'id' => '123',
                    'title' => 'Title',
                    'body' => 'Body',
                ],
            ],
            [
                (new Article())
                    ->setId('123')
                    ->setTitle('Title')
                    ->setDescription('Description')
                    ->setBody('Body')
                    ->setPublishedAt($dateStr)
                    ->setIsLegacyArticle(true),
                // All:
                [
                    'id' => '123',
                    'title' => 'Title',
                    'description' => 'Description',
                    'body' => 'Body',
                    'publishedAt' => $dateStr,
                    'isLegacyArticle' => true,
                ],
            ],
            [
                (new Article())
                    ->setId('123')
                    ->setTitle('Title')
                    ->setDescription('Description')
                    ->setBody('Body')
                    ->setPublishedAt($dateStr)
                    ->setIsLegacyArticle(false),
                // Junk is ignored:
                [
                    'id' => '123',
                    'title' => 'Title',
                    'description' => 'Description',
                    'body' => 'Body',
                    'publishedAt' => $dateStr,
                    'foo' => 'bar',
                    'baz' => 'qux',
                ],
            ],
        ];
    }

    /**
     * @dataProvider providesArticlesCreatedFromArrays
     * @param array<string, mixed> $array
     */
    public function testFromarrayCreatesANewInstance(Article $expectedArticle, array $array): void
    {
        $article = Article::fromArray($array);

        $this->assertEquals($expectedArticle, $article);
    }

    /** @return array<int, array<int, mixed>> */
    public function providesValidArticles(): array
    {
        return [
            [
                false,
                (new Article()),
            ],
            [
                false,
                (new Article())
                    ->setId('foo'),
            ],
            [  // #2
                false,
                (new Article())
                    ->setId('foo')
                    ->setTitle('Title'),
            ],
            [
                false,
                (new Article())
                    ->setId('foo')
                    ->setTitle('Title')
                    ->setBody('Body'),
            ],
            [
                true,
                (new Article())
                    ->setId('foo')
                    ->setTitle('Title')
                    ->setBody('Body')
                    ->setPublishedAt('2022-09-03'),
            ],
            [  // #5
                false,
                (new Article())
                    ->setId('foo')
                    ->setTitle('')  // No title.  Useless.
                    ->setBody('Body')
                    ->setPublishedAt('2022-09-03'),
            ],
            [
                false,
                (new Article())
                    ->setId('foo')
                    ->setTitle('Title')
                    ->setBody('')  // No body.  Useless.
                    ->setPublishedAt('2022-09-03'),
            ],
        ];
    }

    /** @dataProvider providesValidArticles */
    public function testIsvalidReturnsTrueIfTheArticleIsValid(bool $expected, Article $article): void
    {
        $this->assertSame($expected, $article->isValid());
    }
}
