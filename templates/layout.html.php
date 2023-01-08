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
$showWebsiteCarbonBadge = 'dev' !== $config['env'] && $config['show_website_carbon_badge'];
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

        <script>
            if (window.matchMedia) {
                (function () {
                    const mql = window.matchMedia('(prefers-color-scheme: dark)');

                    const applyPreferredColourScheme = function () {
                        document.documentElement.dataset.colourmode = (
                            mql.matches ? 'dark' : 'light');
                    };

                    applyPreferredColourScheme();
                    mql.addEventListener('change', applyPreferredColourScheme);
                })();
            }
        </script>

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

            <footer class="footer">
                <?php /** @var array<string,string> */ $owner = $config['owner'] ?>
                <?= $helper->createCopyrightNotice($site, $owner) ?>

                <div class="footer__spec">
                    <p>Powered by <?= $helper->linkTo('https://github.com/miniblog/engine', 'Miniblog') ?></p>

                    <?php if ($showWebsiteCarbonBadge) : ?>
                        <div id="wcb"></div>
                    <?php endif ?>
                </div>
            </footer>

        </div>

        <?php if ($showWebsiteCarbonBadge) : ?>
            <script>
                if (window.fetch) {
                    (function() {
                        const wcID = (id) => document.getElementById(id);
                        const wcU = encodeURIComponent(window.location.href);
                        const cacheId = `wcb_${wcU}`;

                        const renderResult = function(result) {
                            wcID('wcb_g').innerHTML = result.c + 'g of CO<sub>2</sub>/view';
                            wcID('wcb_2').insertAdjacentHTML('beforeEnd', '; Cleaner than ' + result.p + '% of pages tested');
                        };

                        const makeRequest = function(render = true) {
                            fetch('https://api.websitecarbon.com/b?url=' + wcU)
                                .then(function(response) {
                                    if (!response.ok) {
                                        throw new Error(response);
                                    }

                                    return response.json();
                                })
                                .then(function(result) {
                                    if (render) {
                                        renderResult(result);
                                    }

                                    result.t = Date.now();
                                    localStorage.setItem(cacheId, JSON.stringify(result));
                                })
                                .catch(function(error) {
                                    wcID('wcb_g').innerHTML = 'No Result';
                                    console.log(error);
                                    localStorage.removeItem(cacheId);
                                });
                        };

                        wcID('wcb').insertAdjacentHTML('beforeEnd', `
                        <a href="https://websitecarbon.com">Website Carbon:</a>
                        <span id="wcb_g">Measuring CO<sub>2</sub>&hellip;</span><span id="wcb_2"></span>
                        `);

                        const cachedResponseJson = localStorage.getItem(cacheId);

                        if (cachedResponseJson) {
                            const cachedResponse = JSON.parse(cachedResponseJson);
                            renderResult(cachedResponse);

                            if (Date.now() - cachedResponse.t > 864e5) {
                                makeRequest(false);
                            }
                        } else {
                            makeRequest();
                        }
                    })();
                }
            </script>
        <?php endif ?>
    </body>
</html>
