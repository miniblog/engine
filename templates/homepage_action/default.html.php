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

/** @phpstan-var Config */
$config = $globals->get('config');
/** @var OutputHelper */
$helper = $globals->get('outputHelper');

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
?>
<?php if ($website->getText()) : ?>
    <div class="blurb">
        <?= $website->getText() ?>
    </div>
<?php endif ?>

<div class="blog-postings">
    <h2>Articles</h2>

    <?php /** @var BlogPosting $blogPosting */ foreach ($input['blogPostings'] as $blogPosting) : ?>
        <article>
            <header>
                <h3>
                    <?php /** @var string */ $postingId = $blogPosting->getIdentifier() ?>
                    <?= $helper->linkTo(
                        ['showBlogPosting', ['postingId' => $postingId]],
                        $blogPosting->getHeadline()
                    ) ?>
                </h3>

                <?= $helper->createByLine($blogPosting, $owner, false) ?>
            </header>

            <p><?= $blogPosting->getDescription() ?></p>
        </article>
    <?php endforeach ?>
</div>
