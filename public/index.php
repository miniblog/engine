<?php

declare(strict_types=1);

use Miniblog\Engine\Miniblog;

$projectDir = dirname(__DIR__);

require "{$projectDir}/vendor/autoload.php";

(new Miniblog($projectDir, require "{$projectDir}/config.php"))
    ->run($_GET, $_POST, $_SERVER)
;
