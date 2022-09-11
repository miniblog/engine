<?php

/**
 * Template variables:
 * @var Miniblog\Engine\Article $article
 */

$__layout = 'layout.html.php';

/** @var DateTime $publishedAt */
$publishedAt = $article->getPublishedAt();

echo <<<END
{$article->getTitle()}
{$article->getDescription()}
{$article->getBody()}
{$publishedAt->format('Y-m-d')}
END;
