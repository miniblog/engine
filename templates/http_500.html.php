<?php

use Miniblog\Engine\OutputHelper;

/**
 * Template variables:
 * @var array<string, string> $config
 * @var OutputHelper $helper
 */
?>
<h1>Internal Server Error</h1>

<p>Sorry, but an unexpected error occurred.  Please make your request again and if the problem re-occurs then <a href="<?= "mailto:{$config['contactEmail']}" ?>">please let us know</a>.</p>
