<?php

use Miniblog\Engine\OutputHelper;

/**
 * Template variables:
 * @var array<string, string> $config
 * @var OutputHelper $helper
 * @var Miniblog\Engine\Article $article
 */
?>
<article itemscope itemtype="https://schema.org/BlogPosting">
    <h1 itemprop="headline"><?= $article->getTitle() ?></h1>

    <?php if (!$article->isLegacyArticle()) : ?>
        <?php /** @var DateTime */ $publishedAt = $article->getPublishedAt() ?>
        <p><time datetime="<?= $publishedAt->format('c') ?>" itemprop="datePublished">
            <?= $helper->formatLongDate($publishedAt) ?>
        </time></p>
    <?php endif ?>

    <div itemprop="articleBody">
        <?= $article->getBody() ?>
    </div>

    <footer>
        This blog does not offer comment functionality. If you'd like to discuss any of the topics written about here, you can <a href="<?= "mailto:{$config['contactEmail']}" ?>">email the author</a>.
    </footer>
</article>
