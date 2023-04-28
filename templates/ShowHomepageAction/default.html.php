<?php

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
<article>
    <header>
        <h1 role="presentation">Recent Articles</h1>
    </header>

    <ol reversed data-comfortable>
        <?php /** @var BlogPosting $blogPosting */ foreach ($input['blogPostings'] as $blogPosting) : ?>
            <li>
                <div
                    itemscope
                    itemtype="https://schema.org/BlogPosting"
                >
                    <h2>
                        <?php /** @var string */ $postingId = $blogPosting->getIdentifier() ?>
                        <?= $helper->linkTo(
                            ['showBlogPosting', ['postingId' => $postingId]],
                            ['itemprop' => 'url'],
                            $blogPosting->getHeadline()
                        ) ?>
                    </h2>

                    <p><?= $blogPosting->getDescription() ?></p>

                    <p><small>
                        By <?= $owner->getFullName() ?>
                        on <?= $helper->createDate($blogPosting->getDatePublished(true)) ?>
                    </small></p>
                </div>
            </li>
        <?php endforeach ?>
    </ol>
</article>
