<?php

/**
 * @param string mainContent
 * @param string [metaTitle]
 * @param string [metaDescription]
 */

use DanBettles\Marigold\HttpRequest;
use Miniblog\Engine\OutputHelper;

/** @var array<string,string|string[]> */
$config = $globals->get('config');
/** @var HttpRequest */
$request = $globals->get('request');
/** @var OutputHelper */
$helper = $globals->get('outputHelper');

/** @var array<string,string> */
$site = $config['site'];
$siteTitle = $site['title'];
$siteBlurb = $site['blurb'];

/** @var array{id:string} */
$matchedRoute = $request->attributes['route'] ?? ['id' => ''];
$onHomepage = 'homepage' === $matchedRoute['id'];
?>
<!DOCTYPE html>
<html lang="<?= $site['lang'] ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?= $helper->createMetaTitle(($input['metaTitle'] ?? ''), $siteTitle) ?></title>
        <meta name="description" content="<?= $input['metaDescription'] ?? '' ?>">

        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">

        <style>
            <?= $output->include('stylesheet.css') ?>
        </style>
    </head>

    <body>
        <div class="container">

            <header itemscope itemtype="https://schema.org/WebSite" class="masthead">
                <?php $homepageLink = $helper->linkTo('homepage', $siteTitle) ?>

                <?= $helper->createEl(($onHomepage ? 'h1' : 'p'), [
                    'itemprop' => 'name',
                    'class' => 'masthead__title',
                ], $homepageLink) ?>

                <nav aria-label="Website">
                    <ul>
                        <?php foreach (['Home' => 'homepage'] as $label => $routeId) : ?>
                            <li>
                                <?php if ($matchedRoute['id'] === $routeId) : ?>
                                    <?= $helper->linkTo($routeId, ['class' => 'active'], $label) ?>
                                <?php else : ?>
                                    <?= $helper->linkTo($routeId, $label) ?>
                                <?php endif ?>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </nav>

                <?php if (null !== $siteBlurb) : ?>
                    <p class="masthead__blurb"><?= $siteBlurb ?></p>
                <?php endif ?>
            </header>

            <main>
                <?= $input['mainContent'] ?>
            </main>

            <?php $showWebsiteCarbonBadge = 'dev' !== $config['env'] && $config['show_website_carbon_badge'] ?>

            <footer class="<?= 'footer ' . ($showWebsiteCarbonBadge ? 'footer--with-wcb' : '') ?>">
                <div>
                    <?php /** @var array<string,string> */ $owner = $config['owner'] ?>
                    <?= $helper->createCopyrightNotice($site, $owner) ?>
                    <p class="footer__platform">Powered by <?= $helper->linkTo('https://github.com/miniblog/engine', 'Miniblog') ?></p>
                </div>

                <?php if ($showWebsiteCarbonBadge) : ?>
                    <div>
                        <div id="wcb" class="carbonbadge"></div>
                        <script src="https://unpkg.com/website-carbon-badges@1.1.3/b.min.js" defer></script>
                    </div>
                <?php endif ?>
            </footer>

        </div>
    </body>
</html>
