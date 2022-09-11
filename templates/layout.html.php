<?php

use Miniblog\Engine\OutputHelper;

/**
 * Template variables:
 * @var array<string, string> $config
 * @var OutputHelper $helper
 * @var string $__contentForLayout
 * @var string $metaTitle Optional
 * @var string $metaDescription Optional
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php /*@phpstan-ignore-next-line*/ ?>
        <title><?= (isset($metaTitle) && '' !== $metaTitle ? "{$metaTitle} | " : '') . $config['blogTitle'] ?></title>

        <?php /*@phpstan-ignore-next-line*/ ?>
        <?php if (isset($metaDescription) && '' !== $metaDescription) : ?>
            <meta name="description" content="<?= $metaDescription ?>">
        <?php endif ?>

        <style>
            <?= file_get_contents(__DIR__ . '/stylesheet.css') ?>
        </style>
    </head>

    <body>
        <header>
            <h2 class="blog-title"><a href="/"><?= $config['blogTitle'] ?></a></h2>
            <hr>
        </header>

        <main>
            <?= $__contentForLayout ?>
        </main>
    </body>
</html>
