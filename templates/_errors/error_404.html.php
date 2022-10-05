<?php

$title = 'Page Not Found';

$output->insertInto('layout.html.php', 'mainContent', [
    'metaTitle' => $title,
]);

/** @var array<string, string|string[]> */
$config = $globals->get('config');

/** @var array<string, string> */
$owner = $config['owner'];
?>
<h1><?= $title ?></h1>

<p>Sorry, but we couldn't find the page you requested.  Please double-check the URL and then try again.</p>

<ul>
    <li>If you think this is an error on our part then <a href="<?= "mailto:{$owner['email']}" ?>">please let us know</a>.</li>
    <li>Alternatively, <a href="/">return to the homepage</a> and start over.</li>
</ul>
