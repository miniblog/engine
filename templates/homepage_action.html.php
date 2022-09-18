<?php

use Miniblog\Engine\OutputHelper;

/**
 * Template variables:
 * @var array<string, string|mixed[]> $config
 * @var OutputHelper $helper
 * @var Miniblog\Engine\Article[] $articles
 */

$__layout = 'layout.html.php';

/** @var array<string, string> */
$author = $config['owner'];
?>
<div class="posts">
    <?php foreach ($articles as $article) : ?>
        <article>
            <header>
                <h2>
                    <a href="<?= "/posts/{$article->getId()}" ?>"><?= $article->getTitle() ?></a>
                </h2>

                <?= $helper->createArticleByLine($article, $author, false) ?>
            </header>

            <?php if ($article->getDescription()) : ?>
                <p><?= $article->getDescription() ?></p>
            <?php endif ?>
        </article>
    <?php endforeach ?>
</div>
