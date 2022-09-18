<?php

use Miniblog\Engine\OutputHelper;

/**
 * Template variables:
 * @var array<string, string|string[]> $config
 * @var OutputHelper $helper
 * @var Miniblog\Engine\Article $article
 */

$__layout = 'layout.html.php';

/** @var array<string, string> */
$author = $config['owner'];
$authorEmail = $author['email'];
?>
<article itemscope itemtype="https://schema.org/BlogPosting">
    <header>
        <h1 itemprop="headline"><?= $article->getTitle() ?></h1>
        <?= $helper->createArticleByLine($article, $author) ?>
    </header>

    <div itemprop="articleBody">
        <?= $article->getBody() ?>
    </div>
</article>
