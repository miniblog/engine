<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\FileInfo;
use DirectoryIterator;
use Miniblog\Engine\Schema\Thing;
use Miniblog\Engine\Schema\Thing\CreativeWork\WebSite;
use Miniblog\Engine\Schema\Thing\Person;
use RangeException;
use RuntimeException;

use function file_get_contents;
use function is_dir;
use function preg_match;
use function preg_replace;
use function strlen;
use function substr;

use const false;
use const null;
use const true;

class ThingManager
{
    /**
     * "Miniblog Document".
     *
     * @var string
     */
    private const DOCUMENT_FILE_EXTENSION = 'md';

    /**
     * @access private
     * @var WebSite|null
     */
    public static $thisWebSite;

    /**
     * @access private
     * @var Person|null
     */
    public static $owner;

    private string $namespace;

    private string $dataDir;

    private DocumentParser $documentParser;

    public function __construct(
        string $namespace,
        string $dataDir,
        DocumentParser $documentParser
    ) {
        $this
            ->setNamespace($namespace)
            ->setDataDir($dataDir)
            ->setDocumentParser($documentParser)
        ;
    }

    private function documentFileIsValid(FileInfo $fileInfo): bool
    {
        if (!$fileInfo->isFile()) {
            return false;
        }

        if (self::DOCUMENT_FILE_EXTENSION !== $fileInfo->getExtension()) {
            return false;
        }

        if (!$fileInfo->getSize()) {
            return false;
        }

        $documentId = $fileInfo->getBasenameMinusExtension();

        if (!(bool) preg_match('~^[a-z0-9-]+$~', $documentId)) {
            return false;
        }

        return true;
    }

    /**
     * @phpstan-param class-string $thingClassName
     * @phpstan-param ParsedDocument $parsedDocument
     */
    private function createThingFromArray(string $thingClassName, array $parsedDocument): Thing
    {
        return $thingClassName::createFromArray($parsedDocument['frontMatter']);
    }

    /**
     * Returns `null` if the Document/thing turned out to be invalid.
     *
     * @phpstan-param class-string $thingClassName
     */
    private function loadThing(FileInfo $documentFileInfo, string $thingClassName): ?Thing
    {
        if (!$this->documentFileIsValid($documentFileInfo)) {
            return null;
        }

        /** @var string */
        $documentFileContents = file_get_contents($documentFileInfo->getPathname());
        $parsedDocument = $this->getDocumentParser()->parse($documentFileContents);

        $thing = $this
            ->createThingFromArray($thingClassName, $parsedDocument)
            ->setIdentifier($documentFileInfo->getBasenameMinusExtension())
            ->setBody($parsedDocument['body'])
        ;

        return $thing->isValid()
            ? $thing
            : null
        ;
    }

    /**
     * @phpstan-param class-string $thingClassName
     */
    private function getThingDocumentsDir(string $thingClassName): string
    {
        $relativeClassName = substr($thingClassName, strlen($this->getNamespace()) + 1);
        // (Forward slash or backslash.)
        return $this->getDataDir() . '/' . preg_replace('~[\x2F\x5C]~', '/', $relativeClassName);
    }

    /**
     * @phpstan-param class-string $thingClassName
     */
    public function find(string $thingClassName, string $id): ?Thing
    {
        $documentPathname = $this->getThingDocumentsDir($thingClassName) . "/{$id}." . self::DOCUMENT_FILE_EXTENSION;

        return $this->loadThing(new FileInfo($documentPathname), $thingClassName);
    }

    /**
     * @phpstan-param class-string $thingClassName
     * @return Thing[]
     */
    public function findAll(string $thingClassName): array
    {
        $thingDocumentsDir = $this->getThingDocumentsDir($thingClassName);

        $things = [];

        foreach (new DirectoryIterator($thingDocumentsDir) as $splFileInfo) {
            /** @var FileInfo */
            $fileInfo = $splFileInfo->getFileInfo(FileInfo::class);
            $thing = $this->loadThing($fileInfo, $thingClassName);

            if (!$thing) {
                continue;
            }

            $things[] = $thing;
        }

        return $things;
    }

    /**
     * Returns a Thing that describes this website.
     *
     * @throws RuntimeException If there is something wrong with the Document that describes this website
     */
    public function getThisWebsite(): WebSite
    {
        if (!isset(self::$thisWebSite)) {
            /** @var WebSite|null */
            $thisWebSite = $this->find(WebSite::class, 'this');

            if (!$thisWebSite) {
                throw new RuntimeException('There is something wrong with the Document that describes this website');
            }

            self::$thisWebSite = $thisWebSite;
        }

        return self::$thisWebSite;
    }

    /**
     * Returns a Thing that describes the owner of this website.
     *
     * @throws RuntimeException If there is something wrong with the Document that describes the owner of this website
     */
    public function getOwner(): Person
    {
        if (!isset(self::$owner)) {
            /** @var Person|null */
            $owner = $this->find(Person::class, 'owner');

            if (!$owner) {
                throw new RuntimeException('There is something wrong with the Document that describes the owner of this website');
            }

            self::$owner = $owner;
        }

        return self::$owner;
    }

    private function setNamespace(string $namespace): self
    {
        $this->namespace = $namespace;
        return $this;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @throws RangeException If the directory does not exist
     */
    private function setDataDir(string $dir): self
    {
        if (!is_dir($dir)) {
            throw new RangeException("The directory `{$dir}` does not exist");
        }

        $this->dataDir = $dir;

        return $this;
    }

    public function getDataDir(): string
    {
        return $this->dataDir;
    }

    private function setDocumentParser(DocumentParser $parser): self
    {
        $this->documentParser = $parser;
        return $this;
    }

    public function getDocumentParser(): DocumentParser
    {
        return $this->documentParser;
    }
}
