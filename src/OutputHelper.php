<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\OutputHelper\Html5OutputHelper;
use DanBettles\Marigold\Router;
use DateTime;
use DateTimeInterface;
use IntlDateFormatter;

use function array_filter;
use function implode;
use function is_string;
use function strpos;

use const false;
use const null;
use const true;

class OutputHelper extends Html5OutputHelper
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->setRouter($router);
    }

    /**
     * @param array<string,string|int>|string $parametersOrContent
     */
    public function linkTo(
        string $routeIdOrUrl,
        $parametersOrContent,
        ?string $contentOrNothing = null
    ): string {
        $parameters = $parametersOrContent;
        $content = $contentOrNothing;

        if (is_string($parametersOrContent)) {
            $parameters = [];
            $content = $parametersOrContent;
        }

        /** @var array $parameters */
        /** @var string $content */

        $url = false === strpos($routeIdOrUrl, ':')
            ? $this->router->generatePath($routeIdOrUrl, $parameters)
            : $routeIdOrUrl
        ;

        return $this->createA(['href' => $url], $content);
    }

    private function formatDate(DateTimeInterface $dateTime, int $dateType): string
    {
        $dateFormatter = new IntlDateFormatter(null, $dateType, IntlDateFormatter::NONE);
        /** @var string */
        $formatted = $dateFormatter->format($dateTime);

        return $formatted;
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
        ], $this->formatDate($publishedAt, IntlDateFormatter::MEDIUM));

        return $this->createDiv([
            'class' => 'article__by-line',
        ], "by {$articleAuthorEl} on {$articleDatePublishedEl}");
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

    public function createMetaTitle(string ...$parts): string
    {
        return implode(' | ', array_filter($parts));
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
}
