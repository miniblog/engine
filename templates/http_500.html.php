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

<p>Sorry, but an unexpected error occurred.  Please make your request again and if the problem re-occurs then <a href="<?= "mailto:{$ownerEmail}" ?>">please let us know</a>.</p>
