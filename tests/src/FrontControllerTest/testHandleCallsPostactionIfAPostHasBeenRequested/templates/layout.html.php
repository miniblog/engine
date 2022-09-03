<?php

/**
 * Template variables:
 * @var string $contentForLayout
 */

echo implode("\n", [
    'Before content',
    $contentForLayout,
    'After content',
]);
