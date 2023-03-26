<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use Parsedown;

use function array_slice;
use function explode;
use function implode;
use function json_decode;
use function preg_replace;
use function strlen;
use function substr;

use const false;
use const JSON_THROW_ON_ERROR;
use const null;
use const PHP_EOL;
use const true;

class DocumentParser
{
    private Parsedown $parsedown;

    public function __construct(Parsedown $parsedown)
    {
        $this->setParsedown($parsedown);
    }

    /**
     * @return array{string,string}
     */
    private function splitText(string $text): array
    {
        $openFrontMatter = '{' . PHP_EOL;

        if ($openFrontMatter === substr($text, 0, strlen($openFrontMatter))) {
            $lines = explode(PHP_EOL, $text);

            $previousLineWasRightCurlyBrace = false;
            $numLinesToSlice = 0;

            foreach ($lines as $lineNo => $line) {
                if ($previousLineWasRightCurlyBrace && '' === $line) {
                    $numLinesToSlice = $lineNo;
                    break;
                }

                $previousLineWasRightCurlyBrace = false;

                if ('}' === $line) {
                    $previousLineWasRightCurlyBrace = true;
                }
            }

            if ($numLinesToSlice) {
                return [
                    implode(array_slice($lines, 0, $numLinesToSlice)),
                    implode(PHP_EOL, array_slice($lines, $numLinesToSlice + 1)),
                ];
            }
        }

        return [
            '',
            $text,
        ];
    }

    /**
     * @phpstan-param FrontMatter $frontMatter
     * @phpstan-return ParsedDocument
     */
    private function createResult(
        array $frontMatter = [],
        ?string $bodyHtml = null
    ): array {
        return [
            'frontMatter' => $frontMatter,
            'body' => $bodyHtml,
        ];
    }

    /**
     * @phpstan-return ParsedDocument
     */
    public function parse(string $text): array
    {
        if ('' === $text) {
            return $this->createResult();
        }

        // Normalise newlines.
        /** @var string */
        $text = preg_replace('~\R~', PHP_EOL, $text);

        list(
            $frontMatterJson,
            $markdown
        ) = $this->splitText($text);

        /** @phpstan-var FrontMatter */
        $frontMatter = '' === $frontMatterJson
            ? []
            : json_decode($frontMatterJson, true, 2, JSON_THROW_ON_ERROR)
        ;

        $bodyHtml = '' === $markdown
            ? null
            : $this->getParsedown()->text($markdown)
        ;

        return $this->createResult($frontMatter, $bodyHtml);
    }

    private function setParsedown(Parsedown $parsedown): self
    {
        $this->parsedown = $parsedown;
        return $this;
    }

    public function getParsedown(): Parsedown
    {
        return $this->parsedown;
    }
}
