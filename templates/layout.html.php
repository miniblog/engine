<?php

/**
 * @param string mainContent
 * Optional:
 * @param array<string, string> serverVars
 * @param string metaTitle
 * @param string metaDescription
 */

use Miniblog\Engine\OutputHelper;

/** @var array<string, string|string[]> */
$config = $globals->get('config');
/** @var OutputHelper */
$helper = $globals->get('helper');

/** @var array<string, string> */
$site = $config['site'];
$siteTitle = $site['title'];

/** @var array<string, string> */
$serverVars = $input['serverVars'] ?? [];
$onHomepage = '/' === parse_url($serverVars['REQUEST_URI'] ?? '', PHP_URL_PATH);

/** @var array<string, string> */
$owner = $config['owner'];
?>
<!DOCTYPE html>
<html lang="<?= $site['lang'] ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?= implode(' | ', array_filter([
            $input['metaTitle'] ?? '',
            $siteTitle,
        ])) ?></title>

        <meta name="description" content="<?= $input['metaDescription'] ?? '' ?>">

        <style>
            <?= $output->include('stylesheet.css') ?>
        </style>
    </head>

    <body>
        <div class="container">
            <header itemscope itemtype="https://schema.org/WebSite" class="masthead">
                <?= $helper->createSiteHeading($siteTitle, $onHomepage) ?>
            </header>

            <main>
                <?= $input['mainContent'] ?>
            </main>

            <footer>
                <?= $helper->createCopyrightNotice($site, $owner) ?>
                <p>Powered by <a href="https://github.com/miniblog/engine">Miniblog</a></p>
            </footer>
        </div>
    </body>
</html>
