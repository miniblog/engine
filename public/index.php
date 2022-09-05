<?php

declare(strict_types=1);

use Miniblog\Engine\FrontController;
use Miniblog\Engine\MarkdownParser;

$projectDir = __DIR__ . '/..';

require "{$projectDir}/vendor/autoload.php";

/** @var array<string, string> */
$config = require "{$projectDir}/config.php";
$frontController = new FrontController($config, new MarkdownParser());
$response = $frontController->handle($_SERVER, $_GET);
$response->send($_SERVER);
