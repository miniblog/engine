<?php

declare(strict_types=1);

namespace Miniblog\Engine\Command;

use DanBettles\Marigold\CssMinifier;
use RuntimeException;

use function array_map;
use function file_get_contents;
use function file_put_contents;
use function implode;
use function is_file;
use function passthru;
use function preg_replace;
use function trim;

use const PHP_EOL;

/**
 * Assembles the default CSS stylesheet, which is committed into the engine--and can be overridden by blog projects.
 */
class AssembleDefaultCssCommand extends AbstractCommand
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'assemble-default-css';

    /**
     * @var string[]
     */
    private const SOURCE_STYLESHEETS = [
        'vendor/csstools/sanitize.css/sanitize.css',
        'vendor/csstools/sanitize.css/assets.css',
        'assets/app.css',
    ];

    /**
     * @var string
     */
    private const OUTPUT_STYLESHEET = 'templates/stylesheet.css';

    public function __invoke(): int
    {
        /** @var array<string,mixed> */
        $config = $this->getRegistry()->get('config');
        /** @var string */
        $engineDir = $config['engineDir'];

        $cssMinifier = new CssMinifier();

        $mergedCss = implode(PHP_EOL, array_map(function (string $cssFileRelPathname) use (
            $engineDir,
            $cssMinifier
        ): string {
            $cssFileAbsPathname = "{$engineDir}/{$cssFileRelPathname}";

            if (!is_file($cssFileAbsPathname)) {
                throw new RuntimeException("File `{$cssFileAbsPathname}` does not exist");
            }

            /** @var string */
            $sourceCss = file_get_contents($cssFileAbsPathname);
            $minifiedCss = trim($cssMinifier->removeCommentsFilter($sourceCss));

            return "/* ###> `{$cssFileRelPathname}` ### */" . PHP_EOL . $minifiedCss;
        }, self::SOURCE_STYLESHEETS));

        // Remove superfluous newlines to make the CSS a little more compact.
        $mergedCss = preg_replace('~(\r\n|\r|\n){2,}~', PHP_EOL, $mergedCss) . PHP_EOL;

        $outputFilePathname = "{$engineDir}/" . self::OUTPUT_STYLESHEET;

        if (!(bool) file_put_contents($outputFilePathname, $mergedCss)) {
            throw new RuntimeException("Failed to create `{$outputFilePathname}`");
        }

        // It's not the end of the world if this fails.
        @passthru("git add -v {$outputFilePathname}");

        return self::SUCCESS;
    }
}
