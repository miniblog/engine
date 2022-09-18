<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\OutputHelper\Html5OutputHelper;
use DateTime;
use DateTimeInterface;
use IntlDateFormatter;

use const false;
use const null;
use const true;

class OutputHelper extends Html5OutputHelper
{
    private function formatDate(DateTimeInterface $dateTime, int $dateType): string
    {
        $dateFormatter = new IntlDateFormatter(null, $dateType, IntlDateFormatter::NONE);
        /** @var string */
        $formatted = $dateFormatter->format($dateTime);

        return $formatted;
    }

    /**
     * @param array<string, string> $author
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
     * @param array<string, string> $site
     * @param array<string, string> $owner
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

        return $this->createP("Copyright &copy; {$range} {$owner['name']}");
    }

    public function createSiteHeading(string $title, bool $onHomepage): string
    {
        return $this->createEl(($onHomepage ? 'h1' : 'p'), [
            'itemprop' => 'name',
            'class' => 'masthead__title',
        ], $this->createA(['href' => '/'], $title));
    }
}
