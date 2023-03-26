<?php

// @todo Article microdata.

use Miniblog\Engine\Schema\Thing\CreativeWork\Article;

/** @var Article */
$article = $input['article'];

$output->insertInto('layout.html.php', 'mainContent', [
    'website' => $input['website'],
    'owner' => $input['owner'],
    'metaTitle' => $article->getHeadline(),
    'metaDescription' => $article->getDescription(),
]);
?>
<article>
    <header>
        <h1><?= $article->getHeadline() ?></h1>
    </header>

    <?= $article->getArticleBody() ?>
</article>
