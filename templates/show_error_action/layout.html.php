<?php

/**
 * ASCII art created using https://www.kammerl.de/ascii/AsciiSignature.php
 *
 * @param string metaTitle
 * @param string mainContent
 */

use Miniblog\Engine\OutputHelper;

/** @var array<string,string|string[]> */
$config = $globals->get('config');
/** @var OutputHelper */
$helper = $globals->get('outputHelper');

/** @var array<string,string> */
$site = $config['site'];
$siteTitle = $site['title'];
?>
<!DOCTYPE html>
<html lang="<?= $site['lang'] ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?= $helper->createMetaTitle($input['metaTitle'], $siteTitle) ?></title>
        <meta name="description" content="">

        <style>
            :root {
                --highlight-colour: #caca8b;
                --background-colour: #1e1e1e;
            }

            body {
                font-family: "Courier New", Courier, monospace;
                color: #d7997c;
                background-color: var(--background-colour);
            }

            a:link,
            a:visited,
            a:active {
                font-weight: bold;
                text-decoration: none;
                color: var(--highlight-colour);
            }

            a:hover {
                color: var(--background-colour);
                background-color: var(--highlight-colour);
            }

            a::before {
                content: "< ";
            }

            a::after {
                content: " >";
            }

            main {
                max-width: 80ch;
            }

            .game-over {
                margin-bottom: 3em;
            }

            .game-over pre {
                font-family: inherit;
                font-size: smaller;
                font-weight: bold;
            }

            .game-over__detail {
                margin-top: 2em;
            }

            .confirm__choices > :first-child {
                margin-right: 2ch;
            }
        </style>
    </head>

    <body>
        <main>

            <div class="game-over">
                <pre>
 @@@@@@@@   @@@@@@   @@@@@@@@@@   @@@@@@@@      @@@@@@   @@@  @@@  @@@@@@@@  @@@@@@@
@@@@@@@@@  @@@@@@@@  @@@@@@@@@@@  @@@@@@@@     @@@@@@@@  @@@  @@@  @@@@@@@@  @@@@@@@@
!@@        @@!  @@@  @@! @@! @@!  @@!          @@!  @@@  @@!  @@@  @@!       @@!  @@@
!@!        !@!  @!@  !@! !@! !@!  !@!          !@!  @!@  !@!  @!@  !@!       !@!  @!@
!@! @!@!@  @!@!@!@!  @!! !!@ @!@  @!!!:!       @!@  !@!  @!@  !@!  @!!!:!    @!@!!@!
!!! !!@!!  !!!@!!!!  !@!   ! !@!  !!!!!:       !@!  !!!  !@!  !!!  !!!!!:    !!@!@!
:!!   !!:  !!:  !!!  !!:     !!:  !!:          !!:  !!!  :!:  !!:  !!:       !!: :!!
:!:   !::  :!:  !:!  :!:     :!:  :!:          :!:  !:!   ::!!:!   :!:       :!:  !:!
 ::: ::::  ::   :::  :::     ::    :: ::::     ::::: ::    ::::     :: ::::  ::   :::
 :: :: :    :   : :   :      :    : :: ::       : :  :      :      : :: ::    :   : :</pre>

                <p class="game-over__detail"><?= $input['mainContent'] ?></p>
            </div>

            <div class="confirm">
                <p>What would you like to do?</p>

                <p class="confirm__choices">
                    <?= $helper->linkTo('javascript:window.history.back()', 'Go back') ?>
                    <?= $helper->linkTo('homepage', 'Start over') ?>
                </p>
            </div>

        </main>
    </body>
</html>
