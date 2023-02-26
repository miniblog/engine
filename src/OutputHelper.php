<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\OutputHelper\Html5OutputHelper;
use DanBettles\Marigold\Router;
use DateTime;
use IntlDateFormatter;
use Miniblog\Engine\Schema\Thing\CreativeWork;
use Miniblog\Engine\Schema\Thing\CreativeWork\WebSite;
use Miniblog\Engine\Schema\Thing\Person;

use function array_filter;
use function array_replace;
use function implode;
use function is_array;
use function is_string;
use function strlen;
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

    public function createByLine(
        CreativeWork $creativeWork,
        Person $author,
        bool $inHtmlArticleScope = true
    ): string {
        $personNameEl = $this->createSpan([
            'itemprop' => 'name',
        ], $author->getFullName());

        $creativeWorkAuthorEl = $this->createSpan([
            'itemprop' => ($inHtmlArticleScope ? 'author' : false),
            'itemscope' => true,
            'itemtype' => 'https://schema.org/Person',
        ], $personNameEl);

        /** @var DateTime */
        $creativeWorkPublishedAt = $creativeWork->getDatePublished(true);

        $creativeWorkDatePublishedEl = $this->createTime([
            'datetime' => $creativeWorkPublishedAt->format('c'),
            'itemprop' => ($inHtmlArticleScope ? 'datePublished' : false),
        ], $this->getDateFormatter()->format($creativeWorkPublishedAt));

        $bylineContent = "by {$creativeWorkAuthorEl} on {$creativeWorkDatePublishedEl}";

        if ($creativeWork->getDateModified()) {
            /** @var DateTime */
            $creativeWorkUpdatedAt = $creativeWork->getDateModified(true);

            $creativeWorkDateModifiedEl = $this->createTime([
                'datetime' => $creativeWorkUpdatedAt->format('c'),
                'itemprop' => ($inHtmlArticleScope ? 'dateModified' : false),
            ], $this->getDateFormatter()->format($creativeWorkUpdatedAt));

            $bylineContent .= ", updated on {$creativeWorkDateModifiedEl}";
        }

        return $this->createDiv([
            'class' => 'creative-work__by-line',
        ], $bylineContent);
    }

    public function createCopyrightNotice(
        WebSite $website,
        Person $owner
    ): string {
        /** @var DateTime */
        $websitePublishedAt = $website->getDatePublished(true);
        $siteLaunchYear = $websitePublishedAt->format('Y');
        $thisYear = (new DateTime())->format('Y');

        $range = $siteLaunchYear === $thisYear
            ? $siteLaunchYear
            : "{$siteLaunchYear}-{$thisYear}"
        ;

        $ownerName = $owner->getFullName();

        if (strlen((string) $owner->getEmail())) {
            $ownerName = $this->linkTo("mailto:{$owner->getEmail()}", $ownerName);
        }

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
