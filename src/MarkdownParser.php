<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use Parsedown;

use function array_replace;
use function array_slice;
use function explode;
use function highlight_string;
use function html_entity_decode;
use function implode;
use function ini_get;
use function json_decode;
use function preg_match_all;
use function preg_replace;
use function strlen;
use function strpos;
use function str_replace;
use function substr;
use function trim;

use const ENT_HTML401;
use const ENT_QUOTES;
use const ENT_SUBSTITUTE;
use const false;
use const JSON_THROW_ON_ERROR;
use const null;
use const PHP_EOL;
use const true;

class MarkdownParser
{
    /**
     * @return array{0:?string,1:string}
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
            null,
            $text,
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function createResult(?string $bodyHtml = null): array
    {
        return [
            'body' => $bodyHtml,
        ];
    }

    private function highlightPhp(string $php): string
    {
        if ('' === trim($php)) {
            return $php;
        }

        $phpOpenTag = '<?php';
        $highlighted = '';

        if (false === strpos($php, $phpOpenTag)) {
            // (Assume there's no close-tag in the PHP, either.)
            $highlighted = str_replace(
                '&lt;?php&nbsp;',
                '',
                highlight_string("{$phpOpenTag} {$php}", true)
            );
        } else {
            $highlighted = highlight_string($php, true);
        }

        // Remove trailing slashes from now-void elements.
        $highlighted = str_replace(' />', '>', $highlighted);

        // Peel off the outer layers, which introduce unwanted whitespace and are superfluous in any case.
        /** @var string */
        $highlighted = preg_replace('~^<code>\s*<span[^>]*>\R*(.*?)\R*</span>\s*</code>$~s', '$1', $highlighted);

        $highlightColourClassNames = [
            // 'highlight.bg' => 'php__bg',
            'highlight.comment' => 'php__comment',
            'highlight.default' => 'php__default',
            // 'highlight.html' => 'php__html',
            'highlight.keyword' => 'php__keyword',
            'highlight.string' => 'php__string',
        ];

        foreach ($highlightColourClassNames as $iniName => $className) {
            $colour = ini_get($iniName);

            // Replace inline styles with class names.
            $highlighted = str_replace(
                "style=\"color: {$colour}\"",
                "class=\"{$className}\"",
                $highlighted
            );
        }

        return "<code>{$highlighted}</code>";
    }

    private function highlightCodeInCodeBlocks(string $bodyHtml): string
    {
        $matches = [];
        $matched = (bool) preg_match_all('~<pre><code class="language-php">(.*?)</code></pre>~s', $bodyHtml, $matches);

        if (!$matched) {
            return $bodyHtml;
        }

        foreach ($matches[1] as $matchNo => $encodedPhp) {
            $php = html_entity_decode($encodedPhp, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8');

            $bodyHtml = str_replace(
                $matches[0][$matchNo],
                ('<div class="code-block">' . $this->highlightPhp($php) . '</div>'),
                $bodyHtml
            );
        }

        return $bodyHtml;
    }

    /**
     * @return array<string,mixed>
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

        /** @var array<string,?string> */
        $frontMatter = null === $frontMatterJson
            ? []
            : json_decode($frontMatterJson, true, 2, JSON_THROW_ON_ERROR)
        ;

        $bodyHtml = (new Parsedown())->text($markdown);
        $bodyHtml = $this->highlightCodeInCodeBlocks($bodyHtml);

        $result = array_replace($this->createResult($bodyHtml), $frontMatter);

        return $result;
    }
}
