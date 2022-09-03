<?php

/**
 * Template variables:
 * @var Miniblog\Engine\Article[] $articles
 */

foreach ($articles as $article) {
    /** @var DateTime */
    $publishedAt = $article->getPublishedAt();

    echo implode("\n", [
        $article->getTitle(),
        $article->getDescription(),
        $article->getBody(),
        $publishedAt->format('Y-m-d'),
    ]) . "\n";
}
