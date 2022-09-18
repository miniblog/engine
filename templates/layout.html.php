<?php

use Miniblog\Engine\OutputHelper;

/**
 * Template variables:
 *
 * @var array<string, string|string[]> $config
 * @var OutputHelper $helper
 * @var string $__contentForLayout
 *
 * @var string $serverVars Optional
 * @var string $metaTitle Optional
 * @var string $metaDescription Optional
 */

 /** @var bool $onHomepage */
// @phpstan-ignore-next-line because `$serverVars` really isn't always set
$onHomepage = isset($serverVars) && '/' === parse_url(($serverVars['REQUEST_URI'] ?? ''), PHP_URL_PATH);

/** @var array<string, string> */
$site = $config['site'];
$siteTitle = $site['title'];

/** @var array<string, string> */
$owner = $config['owner'];
?>
<!DOCTYPE html>
<html lang="<?= $site['lang'] ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php /* @phpstan-ignore-next-line because `$metaTitle` really isn't always set */ ?>
        <title><?= (isset($metaTitle) && '' !== $metaTitle ? "{$metaTitle} | " : '') . $siteTitle ?></title>

        <?php /* @phpstan-ignore-next-line because `$metaDescription` really isn't always set */ ?>
        <?php if (isset($metaDescription) && '' !== $metaDescription) : ?>
            <meta name="description" content="<?= $metaDescription ?>">
        <?php endif ?>

        <style>
            <?php /* @todo 'Include' the CSS using the template loader. */ ?>
            <?= file_get_contents(__DIR__ . '/stylesheet.css') ?>
        </style>
    </head>

    <body>
        <div class="container">
            <header itemscope itemtype="https://schema.org/WebSite" class="masthead">
                <?= $helper->createSiteHeading($siteTitle, $onHomepage) ?>
            </header>

            <main>
                <?= $__contentForLayout ?>
            </main>

            <footer>
                <?= $helper->createCopyrightNotice($site, $owner) ?>
                <p>Powered by <a href="https://github.com/miniblog/engine">Miniblog</a></p>
            </footer>
        </div>
    </body>
</html>
