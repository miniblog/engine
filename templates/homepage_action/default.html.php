<?php

/**
 * @param Article|null blurb
 * @param Article[] articles
 */

use Miniblog\Engine\Article;
use Miniblog\Engine\OutputHelper;

/** @var array<string,string|mixed[]> */
$config = $globals->get('config');
/** @var OutputHelper */
$helper = $globals->get('outputHelper');

/** @var array{description:string} */
$site = $config['site'];

$output->insertInto('layout.html.php', 'mainContent', [
    'metaDescription' => $site['description'],
]);

/** @var Article|null */
$blurb = $input['blurb'];

/** @var array<string,string> */
$owner = $config['owner'];
?>
<?php if ($blurb) : ?>
    <div class="blurb">
        <?= $blurb->getBody() ?>
    </div>
<?php endif ?>

<div class="blog-posts">
    <h2>Articles</h2>

    <?php /** @var Article $article */ foreach ($input['articles'] as $article) : ?>
        <?php /** @var string */ $articleId = $article->getId() ?>

        <article>
            <header>
                <h3>
                    <?= $helper->linkTo(
                        ['showBlogPost', ['postId' => $articleId]],
                        $article->getTitle()
                    ) ?>
                </h3>

                <?= $helper->createArticleByLine($article, $owner, false) ?>
            </header>

            <?php if ($article->getDescription()) : ?>
                <p><?= $article->getDescription() ?></p>
            <?php endif ?>
        </article>
    <?php endforeach ?>
</div>
