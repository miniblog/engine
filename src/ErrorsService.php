<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\HttpResponse;

use function strrpos;
use function substr;

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
     * @var array<string,string>
     */
    private array $config;

    /**
     * Lazy loaded.
     *
     * @var array<int,string>
     */
    private array $errorPagePathnames;

    /**
     * @param array<string,string> $config
     */
    public function __construct(array $config)
    {
        $this->setConfig($config);
    }

    public function createRenderPathname(int $statusCode): string
    {
        return "show_error_action/error_{$statusCode}.html.php";
    }

    private function createPagePathname(int $statusCode): string
    {
        $renderPathname = $this->createRenderPathname($statusCode);
        /** @var int */
        $posLastFullStop = strrpos($renderPathname, '.');

        return $this->getConfig()['varDir'] . '/' . substr($renderPathname, 0, $posLastFullStop);
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
     * @param array<string,string> $config
     */
    private function setConfig(array $config): self
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return array<string,string>
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
