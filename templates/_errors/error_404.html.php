<?php

use Miniblog\Engine\OutputHelper;

/**
 * Template variables:
 * @var array<string, string|string[]> $config
 * @var OutputHelper $helper
 * @var string $metaTitle Optional
 */

$__layout = 'layout.html.php';

/** @var array<string, string> */
$owner = $config['owner'];
$ownerEmail = $owner['email'];
?>
<h1><?= $metaTitle ?></h1>

<p>Sorry, but we couldn't find the page you requested.  Please double-check the URL and then try again.</p>

<ul>
    <li>If you think this is an error on our part then <a href="<?= "mailto:{$ownerEmail}" ?>">please let us know</a>.</li>
    <li>Alternatively, <a href="/">return to the homepage</a> and start over.</li>
</ul>
