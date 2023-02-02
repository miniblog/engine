<?php

/**
 * A bog-standard article.
 *
 * @param Article article
 * @param int [firstHeadingLevel = 1]
 */

use Miniblog\Engine\Article;
use Miniblog\Engine\OutputHelper;

/** @var OutputHelper */
$helper = $globals->get('outputHelper');

/** @var Article */
$article = $input['article'];
$firstHeadingLevel = $input['firstHeadingLevel'] ?? 1;
?>
<article itemscope itemtype="https://schema.org/Article">
    <header>
        <?= $helper->createH($firstHeadingLevel, [
            'itemprop' => "headline",
        ], $article->getTitle()) ?>
    </header>

    <div itemprop="articleBody">
        <?= $article->getBody() ?>
    </div>
</article>
