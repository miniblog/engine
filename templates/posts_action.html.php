<?php

use Miniblog\Engine\OutputHelper;

/**
 * Template variables:
 * @var array<string, string> $config
 * @var OutputHelper $helper
 * @var Miniblog\Engine\Article[] $articles
 */
?>
<?php foreach ($articles as $article) : ?>
    <h1 class="list-title">
        <a href="<?= "/?post={$article->getId()}" ?>">
            <?php /** @var DateTime */ $publishedAt = $article->getPublishedAt() ?>
            <?= "{$helper->formatLongDate($publishedAt)} - {$article->getTitle()}" ?>
        </a>
    </h1>
<?php endforeach ?>
