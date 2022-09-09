<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use Miniblog\Engine\Article;
use Miniblog\Engine\ArticleRepository;
use Miniblog\Engine\MarkdownParser;
use RangeException;

use const false;
use const null;
use const true;

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

    /** @return array<int, array<int, mixed>> */
    public function providesSingleArticlesToLoadUsingFind(): array
    {
        return [
            [
                (new Article())
                    ->setId('2022-08-31')
                    ->setTitle('Article Title')
                    ->setDescription(null)
                    ->setBody('<p>Article body</p>')
                    ->setPublishedAt('2022-08-31')  // From article-file basename.
                    ->setIsLegacyArticle(true),
                '2022-08-31',
            ],
            [
                (new Article())
                    ->setId('lorem-ipsum-dolor')
                    ->setTitle('Lorem Ipsum Dolor')
                    ->setDescription(null)
                    ->setBody('<p>Lorem ipsum dolor.</p>')
                    ->setPublishedAt('2022-09-03')
                    ->setIsLegacyArticle(false),
                'lorem-ipsum-dolor',
            ],
            // Invalid because date missing.  The file contains no front matter.
            [
                null,
                '9999-99-99',
            ],
            [
                null,
                'empty-article',
            ],
            [
                null,
                'incomplete-article',
            ],
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

    public function testFindallReturnsAnArrayOfArticlesSortedByRecency(): void
    {
        $dataDir = $this->createFixturePathname(__FUNCTION__);
        $articleRepo = new ArticleRepository(new MarkdownParser(), $dataDir);

        $this->assertEquals([
            // Newest:
            (new Article())
                ->setId('lorem-ipsum-dolor')
                ->setTitle('Lorem Ipsum Dolor')
                ->setDescription(null)
                ->setBody('<p>Lorem ipsum dolor.</p>')
                ->setPublishedAt('2022-09-03')  // From front matter.
                ->setIsLegacyArticle(false),
            // Older:
            (new Article())
                ->setId('2022-08-31')
                ->setTitle('Article Title')
                ->setDescription(null)
                ->setBody('<p>Article body</p>')
                ->setPublishedAt('2022-08-31')  // From basename.
                ->setIsLegacyArticle(true),
        ], $articleRepo->findAll());
    }
}
