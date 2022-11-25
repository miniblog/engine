<?php

/**
 * @param Throwable throwable
 */

use Miniblog\Engine\OutputHelper;

$title = 'Unexpected Error';

$output->insertInto('layout.html.php', 'mainContent', [
    'metaTitle' => $title,
]);

/** @var array<string,string|string[]> */
$config = $globals->get('config');
/** @var OutputHelper */
$helper = $globals->get('outputHelper');

/** @var array<string,string> */
$owner = $config['owner'];
?>
<h1><?= $title ?></h1>

<p>Sorry, but an unexpected error occurred.  Please make your request again and if the problem re-occurs then <?= $helper->linkTo("mailto:{$owner['email']}", 'please let us know') ?>.</p>
