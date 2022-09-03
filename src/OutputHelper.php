<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\OutputHelper\OutputHelperInterface;
use DateTimeInterface;
use IntlDateFormatter;

use const null;

class OutputHelper implements OutputHelperInterface
{
    private function formatDate(DateTimeInterface $dateTime, int $dateType): string
    {
        $dateFormatter = new IntlDateFormatter(null, $dateType, IntlDateFormatter::NONE);
        /** @var string */
        $formatted = $dateFormatter->format($dateTime);

        return $formatted;
    }

    public function formatShortDate(DateTimeInterface $dateTime): string
    {
        return $this->formatDate($dateTime, IntlDateFormatter::MEDIUM);
    }

    public function formatLongDate(DateTimeInterface $dateTime): string
    {
        return $this->formatDate($dateTime, IntlDateFormatter::FULL);
    }
}
