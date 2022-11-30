<?php

declare(strict_types=1);

use DanBettles\Marigold\HttpRequest;
use Miniblog\Engine\Factory;
use Miniblog\Engine\Website;

$projectDir = dirname(__DIR__);

require "{$projectDir}/vendor/autoload.php";

$factory = new Factory($projectDir, HttpRequest::createFromGlobals());

(new Website($factory->getRegistry()))
    ->handleRequest()
;
