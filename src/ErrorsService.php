<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\FileInfo;
use DanBettles\Marigold\HttpResponse;

use function preg_replace;

class ErrorsService
{
    /**
     * @var int[]
     */
    private const SUPPORTED_STATUS_CODES = [
        HttpResponse::HTTP_NOT_FOUND,
        HttpResponse::HTTP_INTERNAL_SERVER_ERROR,
    ];

    /**
     * @phpstan-var Config
     */
    private array $config;

    /**
     * Lazy loaded.
     *
     * @var array<int,string>
     */
    private array $errorPagePathnames;

    /**
     * @phpstan-param Config $config
     */
    public function __construct(array $config)
    {
        $this->setConfig($config);
    }

    public function createRenderPathname(int $statusCode): string
    {
        return "ShowErrorAction/error_{$statusCode}.html.php";
    }

    public function getPageDir(): string
    {
        return $this->getConfig()['projectDir'] . '/public/errors';
    }

    private function createPagePathname(int $statusCode): string
    {
        $renderPathnameInfo = new FileInfo($this->createRenderPathname($statusCode));
        $pageBasename = preg_replace('~[^a-zA-Z0-9.]~', '-', $renderPathnameInfo->getBasenameMinusExtension());

        return "{$this->getPageDir()}/{$pageBasename}";
    }

    /**
     * Returns an array containing the pathnames of all available error pages, indexed by HTTP status code.
     *
     * @return array<int,string>
     */
    public function getPagePathnames(): array
    {
        if (!isset($this->errorPagePathnames)) {
            $this->errorPagePathnames = [];

            foreach (self::SUPPORTED_STATUS_CODES as $statusCode) {
                $this->errorPagePathnames[$statusCode] = $this->createPagePathname($statusCode);
            }
        }

        return $this->errorPagePathnames;
    }

    /**
     * Returns the pathname of the error page for the specified HTTP status code.
     */
    public function getPagePathname(int $statusCode): string
    {
        return $this->getPagePathnames()[$statusCode];
    }

    /**
     * @phpstan-param Config $config
     */
    private function setConfig(array $config): self
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @phpstan-return Config
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
