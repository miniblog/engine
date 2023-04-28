<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\OutputHelper\Html5OutputHelper;
use DanBettles\Marigold\Router;
use DateTime;
use IntlDateFormatter;

use function array_filter;
use function array_replace;
use function implode;
use function is_array;
use function is_string;
use function strpos;
use function uniqid;

use const false;
use const null;

class OutputHelper extends Html5OutputHelper
{
    private Router $router;

    private IntlDateFormatter $dateFormatter;

    public function __construct(Router $router)
    {
        $this
            ->setRouter($router)
            ->setDateFormatter(new IntlDateFormatter(null, IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE))
        ;
    }

    // @todo Extract this
    public function createUniqueName(): string
    {
        return uniqid('u');
    }

    /**
     * @param string|array{0:string,1?:array<string,string>} $routeIdOrUrl
     * @todo Rename this
     */
    private function createPath($routeIdOrUrl): string
    {
        if (is_string($routeIdOrUrl) && false !== strpos($routeIdOrUrl, ':')) {
            return $routeIdOrUrl;
        }

        $generatePathArgs = (array) $routeIdOrUrl;

        return $this->getRouter()->generatePath(...$generatePathArgs);
    }

    /**
     * @param string|array{0:string,1?:array<string,string>} $routeIdOrUrl
     * @param array<string,string>|string|int|float|null $attributesOrContent
     * @param string|int|float|null $contentOrNothing
     */
    public function linkTo(
        $routeIdOrUrl,
        $attributesOrContent = [],
        $contentOrNothing = null
    ): string {
        $url = $this->createPath($routeIdOrUrl);

        $attributes = $attributesOrContent;
        $content = $contentOrNothing;

        if (!is_array($attributesOrContent)) {
            $attributes = [];
            /** @var mixed */
            $content = $attributesOrContent;
        }

        /** @var array<string,string> $attributes */

        return $this->createA(array_replace($attributes, [
            'href' => $url,
        ]), $content);
    }

    /**
     * @param array<string,mixed> $attributes
     */
    public function createDate(
        ?DateTime $datetime,
        array $attributes = []
    ): string {
        if (!$datetime) {
            return '';
        }

        $attributes['datetime'] = $datetime->format('c');

        return $this->createTime($attributes, $this->getDateFormatter()->format($datetime));
    }

    public function createTitle(string ...$parts): string
    {
        return $this->createEl('title', implode(' | ', array_filter($parts)));
    }

    private function setRouter(Router $router): self
    {
        $this->router = $router;
        return $this;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    private function setDateFormatter(IntlDateFormatter $formatter): self
    {
        $this->dateFormatter = $formatter;
        return $this;
    }

    public function getDateFormatter(): IntlDateFormatter
    {
        return $this->dateFormatter;
    }
}
