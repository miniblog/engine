<?php

/**
 * @param WebSite website
 * @param Person author
 * @param BlogPosting blogPosting
 */

use Miniblog\Engine\OutputHelper;
use Miniblog\Engine\Schema\Thing\CreativeWork\Article\SocialMediaPosting\BlogPosting;
use Miniblog\Engine\Schema\Thing\CreativeWork\WebSite;
use Miniblog\Engine\Schema\Thing\Person;

/** @var OutputHelper */
$helper = $globals->get('outputHelper');

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
?>
<article
    itemscope
    itemtype="https://schema.org/BlogPosting"
    class="full blog-posting"
>
    <meta itemprop="inLanguage" content="<?= $blogPosting->getInLanguage() ?: $website->getInLanguage() ?>">

    <header>
        <h1 itemprop="headline"><?= $blogPosting->getHeadline() ?></h1>
        <?= $helper->createByLine($blogPosting, $author) ?>
    </header>

    <div itemprop="articleBody">
        <?= $blogPosting->getArticleBody() ?>
    </div>
</article>
