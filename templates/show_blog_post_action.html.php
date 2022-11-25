<?php

/**
 * @param Article article
 */

use Miniblog\Engine\Article;
use Miniblog\Engine\OutputHelper;

/** @var Article */
$article = $input['article'];

$output->insertInto('layout.html.php', 'mainContent', [
    'metaTitle' => $article->getTitle(),
    'metaDescription' => ($article->getDescription() ?: ''),
]);

/** @var array<string,string|string[]> */
$config = $globals->get('config');
/** @var OutputHelper */
$helper = $globals->get('outputHelper');

/** @var array<string,string> */
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
