<?php

use Miniblog\Engine\OutputHelper;
use Miniblog\Engine\Schema\Thing\CreativeWork\Article\SocialMediaPosting\BlogPosting;
use Miniblog\Engine\Schema\Thing\CreativeWork\WebSite;
use Miniblog\Engine\Schema\Thing\Person;

/** @var WebSite */
$website = $input['website'];
/** @var Person */
$author = $input['author'];
/** @var BlogPosting */
$blogPosting = $input['blogPosting'];

$output->insertInto('layout.html.php', 'mainContent', [
    'website' => $website,
    'owner' => $author,
    'metaTitle' => $blogPosting->getHeadline(),
    'metaDescription' => $blogPosting->getDescription(),
]);

/** @var OutputHelper */
$helper = $globals->get('outputHelper');

$datePublished = $helper->createDate($blogPosting->getDatePublished(true), ['itemprop' => 'datePublished']);
$dateModified = $helper->createDate($blogPosting->getDateModified(true), ['itemprop' => 'dateModified']);
?>
<article
    itemscope
    itemtype="https://schema.org/BlogPosting"
    class="full blog-posting"
>
    <meta itemprop="inLanguage" content="<?= $blogPosting->getInLanguage() ?: $website->getInLanguage() ?>">

    <header>
        <h1 itemprop="headline"><?= $blogPosting->getHeadline() ?></h1>

        <div class="blog-posting__by-line">
            by <span itemscope itemtype="https://schema.org/Person" itemprop="author"><span itemprop="name" class="author__name"><?= $author->getFullName() ?></span></span>
            <?php // @codingStandardsIgnoreStart ?>
            on <?= $datePublished ?><?php if ($dateModified) : ?>, updated on <?= $dateModified ?><?php endif ?>
            <?php // @codingStandardsIgnoreEnd ?>
        </div>
    </header>

    <div itemprop="articleBody">
        <?= $blogPosting->getArticleBody() ?>
    </div>
</article>
