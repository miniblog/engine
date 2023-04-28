<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use Parsedown;

use function highlight_string;
use function ini_get;
use function preg_replace;
use function strpos;
use function str_replace;

use const false;
use const null;
use const true;

class ParsedownExtended extends Parsedown
{
    private function highlightPhp(string $php): string
    {
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

        // Remove trailing slashes from void elements.
        $highlighted = str_replace(' />', '>', $highlighted);

        // The wrapping elements introduce unwanted whitespace; remove them.  The `span` is superfluous, actually.
        /** @var string */
        $highlighted = preg_replace('~^<code>\s*<span[^>]*>\R*(.*?)\R*</span>\s*</code>$~s', '$1', $highlighted);

        $highlightColourClassNames = [
            'highlight.comment' => 'code__comment',
            'highlight.default' => 'code__default',
            'highlight.keyword' => 'code__keyword',
            'highlight.string' => 'code__string',
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

    /**
     * @see parent::element()
     * @param array{name:string,text:array{name:string,text:string,attributes?:array{class:string}}|string} $Element
     */
    protected function element(array $Element): string
    {
        $language = $Element['text']['attributes']['class'] ?? null;

        if (
            'language-php' !== $language
            || 'pre' !== $Element['name']
            || 'code' !== ($Element['text']['name'] ?? null)
        ) {
            return parent::element($Element);
        }

        $code = $Element['text']['text'];

        return (
            '<div class="code-block">' .
            $this->highlightPhp($code) .
            '</div>'
        );
    }
}
