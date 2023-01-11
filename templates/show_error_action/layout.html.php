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

        <?= $helper->createTitle($input['metaTitle'], $siteTitle) ?>

        <style>
            <?= $output->include('show_error_action/stylesheet.css') ?>
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
