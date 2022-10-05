<?php

$title = 'Internal Server Error';

$output->insertInto('layout.html.php', 'mainContent', [
    'metaTitle' => $title,
]);

/** @var array<string, string|string[]> */
$config = $globals->get('config');

/** @var array<string, string> */
$owner = $config['owner'];
?>
<h1><?= $title ?></h1>

<p>Sorry, but an unexpected error occurred.  Please make your request again and if the problem re-occurs then <a href="<?= "mailto:{$owner['email']}" ?>">please let us know</a>.</p>
