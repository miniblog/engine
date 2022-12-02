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
$siteDescription = $site['description'];

/** @var array{id: string} */
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

                <?php if (strlen($siteDescription)) : ?>
                    <p class="masthead__description"><?= $siteDescription ?></p>
                <?php endif ?>
            </header>

            <main>
                <?= $input['mainContent'] ?>
            </main>

            <footer>
                <?php /** @var array<string,string> */ $owner = $config['owner'] ?>
                <?= $helper->createCopyrightNotice($site, $owner) ?>
                <p>Powered by <?= $helper->linkTo('https://github.com/miniblog/engine', 'Miniblog') ?></p>
            </footer>

        </div>
    </body>
</html>
