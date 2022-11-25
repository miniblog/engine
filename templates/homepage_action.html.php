<?php

/**
 * @param Article[] articles
 */

use Miniblog\Engine\Article;
use Miniblog\Engine\OutputHelper;

/** @var array<string,string|mixed[]> */
$config = $globals->get('config');
/** @var OutputHelper */
$helper = $globals->get('outputHelper');

/** @var array{description: string} */
$site = $config['site'];

$output->insertInto('layout.html.php', 'mainContent', [
    'metaDescription' => $site['description'],
]);

/** @var array<string,string> */
$owner = $config['owner'];
?>
<div class="blog-posts">
    <?php /** @var Article $article */ foreach ($input['articles'] as $article) : ?>
        <?php /** @var string */ $articleId = $article->getId() ?>

        <article>
            <header>
                <h2>
                    <?= $helper->linkTo('showBlogPost', ['postId' => $articleId], $article->getTitle()) ?>
                </h2>

                <?= $helper->createArticleByLine($article, $owner, false) ?>
            </header>

            <?php if ($article->getDescription()) : ?>
                <p><?= $article->getDescription() ?></p>
            <?php endif ?>
        </article>
    <?php endforeach ?>
</div>
