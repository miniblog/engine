<?php

declare(strict_types=1);

namespace Miniblog\Engine\Command;

use DanBettles\Marigold\TemplateEngine\Engine;
use Miniblog\Engine\ErrorsService;
use RuntimeException;

use function file_put_contents;

use const false;
use const PHP_EOL;

/**
 * Compiles the error pages for the project.  This command should be run every time the config is changed.
 */
class CompileProjectErrorPagesCommand extends AbstractCommand
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'compile-project-error-pages';

    /**
     * @throws RuntimeException If it failed to create a file.
     */
    public function __invoke(): int
    {
        /** @var ErrorsService */
        $errorsService = $this->get('errorsService');
        $comment = "<!-- N.B. File generated by `{$this->getScriptName()}` -->";
        /** @var Engine */
        $templateEngine = $this->get('templateEngine');

        foreach ($errorsService->getPagePathnames() as $statusCode => $errorPagePathname) {
            $renderPathname = $errorsService->createRenderPathname($statusCode);
            $html = $comment . PHP_EOL . $templateEngine->render($renderPathname);

            $fpcResult = file_put_contents($errorPagePathname, $html);

            if (false === $fpcResult) {
                throw new RuntimeException("Failed to create file `{$errorPagePathname}`.");
            }

            $this->getConsole()->writeLn("Created {$errorPagePathname}");
        }

        return self::SUCCESS;
    }
}
