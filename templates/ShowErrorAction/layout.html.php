<?php

/**
 * ASCII art created using https://www.kammerl.de/ascii/AsciiSignature.php
 */

use Miniblog\Engine\OutputHelper;
use Miniblog\Engine\Schema\Thing\CreativeWork\WebSite;

/** @var OutputHelper */
$helper = $globals->get('outputHelper');

/** @var WebSite */
$website = $input['website'];
?>
<!DOCTYPE html>
<html lang="<?= $website->getInLanguage() ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php /** @var string */ $websiteName = $website->getHeadline() ?>
        <?= $helper->createTitle($input['metaTitle'], $websiteName) ?>

        <style>
            <?= $output->include('ShowErrorAction/stylesheet.css') ?>
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
                    <?= $helper->linkTo('showHomepage', 'Start over') ?>
                </p>
            </div>

        </main>
    </body>
</html>
