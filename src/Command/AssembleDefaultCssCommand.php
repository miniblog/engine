<?php

declare(strict_types=1);

namespace Miniblog\Engine\Command;

use Exception;
use Miniblog\Engine\AbstractCommand;
use Miniblog\Engine\Console;
use RuntimeException;

use function array_combine;
use function array_map;
use function file_get_contents;
use function file_put_contents;
use function implode;
use function is_file;
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
    public const COMMAND_NAME = 'dev:assemble-default-css';

    /**
     * @var string[]
     */
    private array $sourceStylesheetPathnames;

    private string $outputFilePathname;

    public function __construct(Console $console)
    {
        parent::__construct($console);

        /** @phpstan-var Config */
        $config = $this->get('config');

        /** @var string */
        $engineDir = $config['engineDir'];
        /** @var string */
        $engineTemplatesDir = $config['engineTemplatesDir'];

        // Config.
        $cssFileRelPathnames = [
            'vendor/csstools/sanitize.css/sanitize.css',
            'vendor/csstools/sanitize.css/assets.css',
            'assets/app.css',
        ];

        $this->sourceStylesheetPathnames = array_map(function (string $relPathname) use ($engineDir): string {
            return "{$engineDir}/{$relPathname}";
        }, array_combine($cssFileRelPathnames, $cssFileRelPathnames));

        // Config.
        $this->outputFilePathname = "{$engineTemplatesDir}/stylesheet.css";
    }

    public function __invoke(): int
    {
        $sourceStylesheets = [];

        foreach ($this->sourceStylesheetPathnames as $relPathname => $absPathname) {
            if (!is_file($absPathname)) {
                throw new RuntimeException("File `{$absPathname}` does not exist");
            }

            /** @var string */
            $sourceCss = file_get_contents($absPathname);

            $sourceStylesheets[] = "/* ###> {$relPathname} ### */" . PHP_EOL . PHP_EOL . trim($sourceCss);
        }

        $mergedCss = implode(PHP_EOL . PHP_EOL, $sourceStylesheets);

        // Remove superfluous newlines to make the CSS neater and a little more compact.
        $mergedCss = preg_replace('~\R{3,}~', PHP_EOL, $mergedCss) . PHP_EOL;

        if (!(bool) file_put_contents($this->outputFilePathname, $mergedCss)) {
            throw new RuntimeException("Failed to create `{$this->outputFilePathname}`");
        }

        $this->getConsole()->writeLn("Created `{$this->outputFilePathname}`");

        // It's not the end of the world if this fails.
        try {
            $this->getConsole()->passthru("git add -v {$this->outputFilePathname}");
        } catch (Exception $ignore) {
        }

        return self::SUCCESS;
    }
}
