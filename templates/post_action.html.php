<?php

use Miniblog\Engine\OutputHelper;

/**
 * Template variables:
 * @var array<string, string> $config
 * @var OutputHelper $helper
 * @var Miniblog\Engine\Article $article
 */

$__layout = 'layout.html.php';
?>
<article itemscope itemtype="https://schema.org/BlogPosting">
    <h1 itemprop="headline"><?= $article->getTitle() ?></h1>

    <?php /** @var DateTime */ $publishedAt = $article->getPublishedAt() ?>
    <p><time datetime="<?= $publishedAt->format('c') ?>" itemprop="datePublished">
        <?= $helper->formatShortDate($publishedAt) ?>
    </time></p>

    <div itemprop="articleBody">
        <?= $article->getBody() ?>
    </div>
</article>
