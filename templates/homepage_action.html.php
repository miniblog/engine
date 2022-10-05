<?php

/**
 * @param array<string, string> serverVars
 * @param Miniblog\Engine\Article[] articles
 */

use Miniblog\Engine\OutputHelper;

/** @var array<string, string|mixed[]> */
$config = $globals->get('config');
/** @var OutputHelper */
$helper = $globals->get('helper');
/** @var array{description: string} */
$site = $config['site'];

$output->insertInto('layout.html.php', 'mainContent', [
    'serverVars' => $input['serverVars'],
    'metaDescription' => $site['description'],
]);

/** @var Miniblog\Engine\Article[] */
$articles = $input['articles'];

/** @var array<string, string> */
$owner = $config['owner'];
?>
<div class="blog-posts">
    <?php foreach ($articles as $article) : ?>
        <article>
            <header>
                <h2>
                    <a href="<?= "/blog/{$article->getId()}" ?>"><?= $article->getTitle() ?></a>
                </h2>

                <?= $helper->createArticleByLine($article, $owner, false) ?>
            </header>

            <?php if ($article->getDescription()) : ?>
                <p><?= $article->getDescription() ?></p>
            <?php endif ?>
        </article>
    <?php endforeach ?>
</div>
