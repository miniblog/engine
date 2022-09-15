<?php

use Miniblog\Engine\OutputHelper;

/**
 * Template variables:
 * @var array<string, string> $config
 * @var OutputHelper $helper
 * @var string $metaTitle Optional
 */

$__layout = 'layout.html.php';
?>
<h1><?= $metaTitle ?></h1>

<p>Sorry, but an unexpected error occurred.  Please make your request again and if the problem re-occurs then <a href="<?= "mailto:{$config['author']['email']}" ?>">please let us know</a>.</p>
