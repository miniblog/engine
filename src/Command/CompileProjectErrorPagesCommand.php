<?php

declare(strict_types=1);

namespace Miniblog\Engine\Command;

use DanBettles\Marigold\TemplateEngine\Engine;
use DOMDocument;
use Miniblog\Engine\AbstractCommand;
use Miniblog\Engine\ErrorsService;
use Miniblog\Engine\ThingManager;
use RuntimeException;

use function file_put_contents;
use function libxml_use_internal_errors;
use function sprintf;

use const false;
use const LIBXML_NOBLANKS;
use const true;

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
     * @throws RuntimeException If it failed to create a file
     */
    public function __invoke(array $options = []): int
    {
        /** @var ErrorsService */
        $errorsService = $this->get('errorsService');
        /** @var Engine */
        $templateEngine = $this->get('templateEngine');
        /** @var ThingManager */
        $thingManager = $this->get('thingManager');

        $this->getConsole()->passthru(sprintf('rm --force %s/*.*', $errorsService->getPageDir()));

        foreach ($errorsService->getPagePathnames() as $statusCode => $errorPagePathname) {
            $renderPathname = $errorsService->createRenderPathname($statusCode);

            $html = $templateEngine->render($renderPathname, [
                'website' => $thingManager->getThisWebsite(),
            ]);

            $html = $this->minifyWebPage($html);

            $fpcResult = file_put_contents($errorPagePathname, $html);

            if (false === $fpcResult) {
                throw new RuntimeException("Failed to create file `{$errorPagePathname}`");
            }

            $this->getConsole()->writeLn("Created `{$errorPagePathname}`");
        }

        return self::SUCCESS;
    }

    /**
     * @throws RuntimeException If it failed to save the minified web page
     */
    private function minifyWebPage(string $html): string
    {
        $prevUseLibxmlInternalErrors = libxml_use_internal_errors(true);

        $domDocument = new DOMDocument();
        $domDocument->formatOutput = false;
        $domDocument->loadHTML($html, LIBXML_NOBLANKS);
        $html = $domDocument->saveHTML();

        libxml_use_internal_errors($prevUseLibxmlInternalErrors);

        if (false === $html) {
            throw new RuntimeException('Failed to save the minified web page');
        }

        return $html;
    }
}
