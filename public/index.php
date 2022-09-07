<?php

declare(strict_types=1);

use Miniblog\Engine\FrontController;
use Miniblog\Engine\MarkdownParser;

$projectDir = dirname(__DIR__);

require "{$projectDir}/vendor/autoload.php";

$frontControllerClass = new ReflectionClass(FrontController::class);
/** @var string */
$classPathName = $frontControllerClass->getFileName();
$engineDir = dirname(dirname($classPathName));

/** @var array<string, string> */
$config = array_replace(require "{$projectDir}/config.php", [
    'projectDir' => $projectDir,
    'engineDir' => $engineDir,
]);

$frontController = $frontControllerClass->newInstance($config, new MarkdownParser());
$response = $frontController->handle($_SERVER, $_GET);
$response->send($_SERVER);
