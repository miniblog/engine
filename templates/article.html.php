<?php

use Miniblog\Engine\OutputHelper;
use Miniblog\Engine\Schema\Thing\CreativeWork\Article;
use Miniblog\Engine\Schema\Thing\CreativeWork\WebSite;
use Miniblog\Engine\Schema\Thing\Person;

/** @var Article */
$article = $input['article'];
/** @var OutputHelper */
$helper = $globals->get('outputHelper');

/** @var DateTime */
$publishedAt = $article->getDatePublished(true);
$datePublished = $helper->createDate($publishedAt, ['itemprop' => 'datePublished']);
$dateModified = $helper->createDate($article->getDateModified(true), ['itemprop' => 'dateModified']);

/** @var WebSite */
$website = $input['website'];
/** @var Person */
$author = $input['author'];
?>
<article itemscope itemtype="<?= $input['itemType'] ?>">
    <meta itemprop="inLanguage" content="<?= $article->getInLanguage() ?: $website->getInLanguage() ?>">

    <header>
        <h1 itemprop="headline"><?= $article->getHeadline() ?></h1>
    </header>

    <div itemprop="articleBody">
        <?= $article->getArticleBody() ?>
    </div>

    <hr>

    <footer>
        <address><small>
            Copyright &copy; <?= $publishedAt->format('Y') ?>
            <a
                href="<?= "mailto:{$author->getEmail()}" ?>"
                itemprop="author"
                itemscope
                itemtype="https://schema.org/Person"
            ><span itemprop="name"><?= $author->getFullName() ?></span></a>.

            Published <?= $datePublished ?><?= $dateModified ? ", updated {$dateModified}." : '' ?>
        </small></address>
    </footer>
</article>
