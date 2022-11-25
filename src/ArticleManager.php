<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use RangeException;

use function is_dir;

class ArticleManager
{
    private MarkdownParser $markdownParser;

    private string $dataDir;

    /**
     * @var array<string,ArticleRepository>
     */
    private array $repositories;

    public function __construct(
        MarkdownParser $markdownParser,
        string $dataDir
    ) {
        $this
            ->setMarkdownParser($markdownParser)
            ->setDataDir($dataDir)
        ;
    }

    public function getRepository(string $type): ArticleRepository
    {
        if (!isset($this->repositories[$type])) {
            $this->repositories[$type] = new ArticleRepository(
                $this->getMarkdownParser(),
                "{$this->getDataDir()}/{$type}"
            );
        }

        return $this->repositories[$type];
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
}
