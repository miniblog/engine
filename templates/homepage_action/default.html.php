<?php

/**
 * @param WebSite website
 * @param Person owner
 * @param BlogPosting[] blogPostings
 */

use Miniblog\Engine\OutputHelper;
use Miniblog\Engine\Schema\Thing\CreativeWork\Article\SocialMediaPosting\BlogPosting;
use Miniblog\Engine\Schema\Thing\CreativeWork\WebSite;
use Miniblog\Engine\Schema\Thing\Person;

/** @var WebSite */
$website = $input['website'];
/** @var Person */
$owner = $input['owner'];

$output->insertInto('layout.html.php', 'mainContent', [
    'website' => $website,
    'owner' => $owner,
    'metaTitle' => '',
    'metaDescription' => $website->getDescription(),
]);

/** @var OutputHelper */
$helper = $globals->get('outputHelper');
?>
<?php if ($website->getText()) : ?>
    <div class="blurb">
        <?= $website->getText() ?>
    </div>
<?php endif ?>

<div class="blog-postings">
    <h2>Articles</h2>

    <?php /** @var BlogPosting $blogPosting */ foreach ($input['blogPostings'] as $blogPosting) : ?>
        <article
            itemscope
            itemtype="https://schema.org/BlogPosting"
            class="summary blog-posting"
        >
            <header>
                <h3>
                    <?php /** @var string */ $postingId = $blogPosting->getIdentifier() ?>
                    <?= $helper->linkTo(
                        ['showBlogPosting', ['postingId' => $postingId]],
                        ['itemprop' => 'url'],
                        $blogPosting->getHeadline()
                    ) ?>
                </h3>

                <div class="blog-posting__by-line">
                    by <span class="author__name"><?= $owner->getFullName() ?></span>
                    on <?= $helper->createDate($blogPosting->getDatePublished(true)) ?>
                </div>
            </header>

            <p><?= $blogPosting->getDescription() ?></p>
        </article>
    <?php endforeach ?>
</div>
