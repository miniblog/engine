<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use Miniblog\Engine\ArticleManager;
use Miniblog\Engine\ArticleRepository;
use Miniblog\Engine\MarkdownParser;
use RangeException;

class ArticleManagerTest extends AbstractTestCase
{
    public function testIsInstantiable(): void
    {
        $markdownParser = $this->createStub(MarkdownParser::class);
        $dataDir = $this->createFixturePathname(__FUNCTION__);

        $manager = new ArticleManager($markdownParser, $dataDir);

        $this->assertSame($markdownParser, $manager->getMarkdownParser());
        $this->assertSame($dataDir, $manager->getDataDir());
    }

    public function testConstructorThrowsAnExceptionIfTheDataDirectoryDoesNotExist(): void
    {
        $dataDir = $this->createFixturePathname('non_existent_dir');

        $this->expectException(RangeException::class);
        $this->expectExceptionMessage("The directory `{$dataDir}` does not exist.");

        new ArticleManager($this->createStub(MarkdownParser::class), $dataDir);
    }

    public function testGetrepositoryReturnsAnArticleRepository(): void
    {
        $dataDir = $this->createFixturePathname(__FUNCTION__);

        $manager = new ArticleManager(
            $this->createStub(MarkdownParser::class),
            $dataDir
        );

        $blogPostRepo = $manager->getRepository('BlogPost');

        $this->assertInstanceOf(ArticleRepository::class, $blogPostRepo);
        $this->assertSame("{$dataDir}/BlogPost", $blogPostRepo->getDataDir());
    }

    public function testGetrepositoryAlwaysReturnsTheSameRepositoryForAGivenType(): void
    {
        $manager = new ArticleManager(
            $this->createStub(MarkdownParser::class),
            $this->createFixturePathname(__FUNCTION__)
        );

        $blogPostRepo = $manager->getRepository('BlogPost');

        $this->assertSame($blogPostRepo, $manager->getRepository('BlogPost'));
    }
}
