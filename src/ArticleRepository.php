<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\File\FileInfo;
use DateTime;
use DirectoryIterator;
use RangeException;

use function array_key_exists;
use function array_merge;
use function array_replace;
use function array_values;
use function file_get_contents;
use function is_dir;
use function krsort;
use function preg_match;

use const null;

class ArticleRepository
{
    /**
     * @var string
     */
    private const ARTICLE_FILE_EXTENSION = 'md';

    private MarkdownParser $markdownParser;

    private string $dataDir;

    public function __construct(
        MarkdownParser $markdownParser,
        string $dataDir
    ) {
        $this
            ->setMarkdownParser($markdownParser)
            ->setDataDir($dataDir)
        ;
    }

    /**
     * Returns a list of all valid-looking article files in the directory.
     *
     * @return array<string, FileInfo>
     */
    private function listArticleFiles(): array
    {
        $articleFiles = [];

        foreach (new DirectoryIterator($this->getDataDir()) as $splFileInfo) {
            /** @var FileInfo */
            $fileInfo = $splFileInfo->getFileInfo(FileInfo::class);

            if (!$fileInfo->isFile()) {
                continue;
            }

            if (self::ARTICLE_FILE_EXTENSION !== $fileInfo->getExtension()) {
                continue;
            }

            if (!$fileInfo->getSize()) {
                continue;
            }

            $articleId = $fileInfo->getBasenameMinusExtension();

            // @todo Extract this?
            if (!(bool) preg_match('~^[a-z0-9-]+$~', $articleId)) {
                continue;
            }

            $articleFiles[$articleId] = $fileInfo;
        }

        return $articleFiles;
    }

    /**
     * Returns `null` if the article turned out to be invalid.
     */
    private function loadArticleFile(FileInfo $articleFile): ?Article
    {
        $articleId = $articleFile->getBasenameMinusExtension();

        /** @var string */
        $text = file_get_contents($articleFile->getPathname());
        $parsedMarkdown = $this->getMarkdownParser()->parse($text);

        $article = Article::fromArray(array_replace([
            'id' => $articleId,
        ], $parsedMarkdown));

        return $article->isValid()
            ? $article
            : null
        ;
    }

    public function find(string $id): ?Article
    {
        $articleFiles = $this->listArticleFiles();

        return array_key_exists($id, $articleFiles)
            ? $this->loadArticleFile($articleFiles[$id])
            : null
        ;
    }

    /**
     * @return Article[]
     */
    public function findAll(): array
    {
        $articleFiles = $this->listArticleFiles();

        if (!$articleFiles) {
            return [];
        }

        $articlesWithDates = [];
        // @todo Remove this!!
        $articlesWithoutDates = [];

        foreach ($articleFiles as $articleFile) {
            $article = $this->loadArticleFile($articleFile);

            if (null === $article) {
                continue;
            }

            /** @var DateTime */
            $publishedAt = $article->getPublishedAt();
            $articlesWithDates[$publishedAt->format('c')] = $article;
        }

        krsort($articlesWithDates);

        return array_merge(
            array_values($articlesWithDates),
            $articlesWithoutDates
        );
    }

    /**
     * @throws RangeException If the directory does not exist.
     */
    private function setDataDir(string $dataDir): self
    {
        if (!is_dir($dataDir)) {
            throw new RangeException("The directory `{$dataDir}` does not exist.");
        }

        $this->dataDir = $dataDir;

        return $this;
    }

    public function getDataDir(): string
    {
        return $this->dataDir;
    }

    private function setMarkdownParser(MarkdownParser $parser): self
    {
        $this->markdownParser = $parser;
        return $this;
    }

    public function getMarkdownParser(): MarkdownParser
    {
        return $this->markdownParser;
    }
}
