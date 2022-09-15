<?php

use Miniblog\Engine\OutputHelper;

/**
 * Template variables:
 * @var array<string, string> $config
 * @var OutputHelper $helper
 * @var Miniblog\Engine\Article[] $articles
 */

$__layout = 'layout.html.php';
?>
<?php foreach ($articles as $article) : ?>
    <h1 class="list-title">
        <a href="<?= "/posts/{$article->getId()}" ?>">
            <?php /** @var DateTime */ $publishedAt = $article->getPublishedAt() ?>
            <?= "{$helper->formatShortDate($publishedAt)} - {$article->getTitle()}" ?>
        </a>
    </h1>
<?php endforeach ?>
