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

use const false;
use const null;
use const true;

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
        $generatePathArgs = null;

        if (is_string($routeIdOrUrl) && false === strpos($routeIdOrUrl, ':')) {
            $generatePathArgs = [$routeIdOrUrl];
        } elseif (is_array($routeIdOrUrl)) {
            $generatePathArgs = $routeIdOrUrl;
        }

        $url = null === $generatePathArgs
            ? $routeIdOrUrl
            : $this->router->generatePath(...$generatePathArgs)
        ;

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
     * @param array<string,string> $author
     */
    public function createArticleByLine(
        Article $article,
        array $author,
        bool $inArticleScope = true
    ): string {
        $personNameEl = $this->createSpan([
            'itemprop' => 'name',
        ], $author['name']);

        $articleAuthorEl = $this->createSpan([
            'itemprop' => ($inArticleScope ? 'author' : false),
            'itemscope' => true,
            'itemtype' => 'https://schema.org/Person',
        ], $personNameEl);

        /** @var DateTime */
        $publishedAt = $article->getPublishedAt();

        $articleDatePublishedEl = $this->createTime([
            'datetime' => $publishedAt->format('c'),
            'itemprop' => ($inArticleScope ? 'datePublished' : false),
        ], $this->getDateFormatter()->format($publishedAt));

        $bylineContent = "by {$articleAuthorEl} on {$articleDatePublishedEl}";

        if ($article->getUpdatedAt()) {
            /** @var DateTime */
            $updatedAt = $article->getUpdatedAt();

            $articleDateUpdatedEl = $this->createTime([
                'datetime' => $updatedAt->format('c'),
                'itemprop' => ($inArticleScope ? 'dateModified' : false),
            ], $this->getDateFormatter()->format($updatedAt));

            $bylineContent .= ", updated on {$articleDateUpdatedEl}";
        }

        return $this->createDiv([
            'class' => 'article__by-line',
        ], $bylineContent);
    }

    /**
     * @param array<string,string> $site
     * @param array<string,string> $owner
     */
    public function createCopyrightNotice(
        array $site,
        array $owner
    ): string {
        $siteLaunchYear = (new DateTime($site['publishedOn']))->format('Y');
        $thisYear = (new DateTime())->format('Y');

        $range = $siteLaunchYear === $thisYear
            ? $siteLaunchYear
            : "{$siteLaunchYear}-{$thisYear}"
        ;

        $ownerName = $this->linkTo("mailto:{$owner['email']}", $owner['name']);

        return $this->createP("Copyright &copy; {$range} {$ownerName}");
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
