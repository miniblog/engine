<?php

use Miniblog\Engine\Article;

$output->insertInto('layout.html.php', 'contentForLayout');

/** @var Article[] */
$articles = $input['articles'];

foreach ($articles as $article) {
    /** @var DateTime */
    $publishedAt = $article->getPublishedAt();

    // phpcs:ignore
    echo <<<END
    {$article->getTitle()}
    {$article->getDescription()}
    {$article->getBody()}
    {$publishedAt->format('Y-m-d')}

    END;
}
