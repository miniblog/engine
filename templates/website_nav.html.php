<?php

use Miniblog\Engine\OutputHelper;
use Miniblog\Engine\WebsiteNav;

/** @var WebsiteNav */
$websiteNav = $globals->get('websiteNav');
/** @var OutputHelper */
$helper = $globals->get('outputHelper');

$stepsToCurrent = $websiteNav->getStepsToCurrent();

$lastStepIdx = count($stepsToCurrent) - 1;
$breadcrumbTrailAnchors = [];

foreach ($stepsToCurrent as $i => $menuItem) {
    $attributes = [];

    if ($lastStepIdx === $i) {
        $attributes['aria-current'] = 'page';
    }

    $breadcrumbTrailAnchors[] = $helper->linkTo($menuItem['routeId'], $attributes, $menuItem['content']);
}

$childrenOfCurrent = $websiteNav->getChildrenOfCurrent();
?>
<nav aria-label="Website">
    <ul>
        <li>
            <h1><?= implode('&raquo;', $breadcrumbTrailAnchors) ?></h1>
        </li>

        <?php if ($childrenOfCurrent) : ?>
            <li>
                <ul>
                    <?php foreach ($childrenOfCurrent as $menuItem) : ?>
                        <li><?= $helper->linkTo($menuItem['routeId'], $menuItem['content']) ?></li>
                    <?php endforeach ?>
                </ul>
            </li>
        <?php endif ?>
    </ul>
</nav>
