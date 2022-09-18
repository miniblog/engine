<?php

/**
 * Template variables:
 * @var Miniblog\Engine\Article[] $articles
 */

$__layout = 'layout.html.php';

foreach ($articles as $article) {
    /** @var DateTime */
    $publishedAt = $article->getPublishedAt();

    echo <<<END
    {$article->getTitle()}
    {$article->getDescription()}
    {$article->getBody()}
    {$publishedAt->format('Y-m-d')}

    END;
}
