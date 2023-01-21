<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use Miniblog\Engine\Article;
use Miniblog\Engine\ArticleRepository;
use Miniblog\Engine\MarkdownParser;
use RangeException;

use const null;

class ArticleRepositoryTest extends AbstractTestCase
{
    public function testIsInstantiable(): void
    {
        $markdownParser = new MarkdownParser();
        $dataDir = $this->createFixturePathname(__FUNCTION__);

        $articleRepo = new ArticleRepository($markdownParser, $dataDir);

        $this->assertSame($markdownParser, $articleRepo->getMarkdownParser());
        $this->assertSame($dataDir, $articleRepo->getDataDir());
    }

    public function testConstructorThrowsAnExceptionIfTheDataDirectoryDoesNotExist(): void
    {
        $dataDir = $this->createFixturePathname('non_existent_dir');

        $this->expectException(RangeException::class);
        $this->expectExceptionMessage("The directory `{$dataDir}` does not exist.");

        new ArticleRepository(new MarkdownParser(), $dataDir);
    }

    /** @return array<mixed[]> */
    public function providesSingleArticlesToLoadUsingFind(): array
    {
        return [
            [
                (new Article())
                    ->setId('minimum-article')
                    ->setTitle('Minimum Article')
                    ->setDescription(null)
                    ->setBody('<p>Lorem ipsum dolor.</p>')
                    ->setPublishedAt('2022-09-03'),
                'minimum-article',
            ],
            [
                (new Article())
                    ->setId('maximum-article')
                    ->setTitle('Maximum Article')
                    ->setDescription('Description')
                    ->setBody('<p>Lorem ipsum dolor.</p>')
                    ->setPublishedAt('2022-09-14'),
                'maximum-article',
            ],
            // DSB-format articles are invalid.
            [
                null,
                '2022-08-31',
            ],
            [
                null,
                'missing-title',
            ],
            [
                null,
                'missing-published-date',
            ],
            [
                null,
                'empty-article',
            ],
            // Valid except for the ID.
            [
                null,
                'Invalid_Id',
            ],
        ];
    }

    /**
     * @dataProvider providesSingleArticlesToLoadUsingFind
     * @param ?Article $expectedArticle
     * @param string $articleId
     */
    public function testFindLoadsASingleArticleById($expectedArticle, $articleId): void
    {
        $articleRepo = new ArticleRepository(
            new MarkdownParser(),
            $this->createFixturePathname(__FUNCTION__)
        );

        $this->assertEquals($expectedArticle, $articleRepo->find($articleId));
    }

    public function testFindReturnsNullIfTheArticleDoesNotExist(): void
    {
        $articleRepo = new ArticleRepository(
            new MarkdownParser(),
            $this->createFixturePathname(__FUNCTION__)
        );

        $this->assertNull($articleRepo->find('non_existent'));
    }

    public function testFindReturnsNullIfTheIdIsInvalid(): void
    {
        $dataDir = $this->createFixturePathname(__FUNCTION__);

        $invalidId = 'Invalid_Id';
        $articleFilePathname = "{$dataDir}/{$invalidId}.md";

        // The file *does* exist...
        $this->assertFileExists($articleFilePathname);

        $articleRepo = new ArticleRepository(new MarkdownParser(), $dataDir);

        // ...But it won't be returned because its ID is invalid.
        $this->assertNull($articleRepo->find($invalidId));
    }

    public function testFindallReturnsAnArrayOfValidArticlesSortedByRecency(): void
    {
        $dataDir = $this->createFixturePathname(__FUNCTION__);
        $articleRepo = new ArticleRepository(new MarkdownParser(), $dataDir);

        $this->assertEquals([
            // Newest:
            (new Article())
                ->setId('maximum-article')
                ->setTitle('Maximum Article')
                ->setDescription('Description')
                ->setBody('<p>Lorem ipsum dolor.</p>')
                ->setPublishedAt('2022-09-14'),
            // Older:
            (new Article())
                ->setId('minimum-article')
                ->setTitle('Minimum Article')
                ->setDescription(null)
                ->setBody('<p>Lorem ipsum dolor.</p>')
                ->setPublishedAt('2022-09-03'),
        ], $articleRepo->findAll());
    }
}
