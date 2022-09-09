<?php

/**
 * Template variables:
 * @var Miniblog\Engine\Article $article
 */

/** @var DateTime $publishedAt */
$publishedAt = $article->getPublishedAt();

echo implode("\n", [
    $article->getTitle(),
    $article->getDescription(),
    $article->getBody(),
    $publishedAt->format('Y-m-d'),
]);
