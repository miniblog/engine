<?php

use DanBettles\Marigold\HttpRequest;
use Miniblog\Engine\OutputHelper;
use Miniblog\Engine\ThingManager;

/** @var ThingManager */
$thingManager = $globals->get('thingManager');

$websiteMenuItems = [
    ['showHomepage', 'Home'],
];

if (null !== $thingManager->getAboutThisWebsite()) {
    $websiteMenuItems[] = ['showAboutWebsite', 'About'];
}

$websiteMenuItems[] = ['signUp', 'Subscribe'];

/** @var HttpRequest */
$request = $globals->get('request');
/** @phpstan-var MatchedRoute */
$matchedRoute = $request->attributes['route'];
/** @var OutputHelper */
$helper = $globals->get('outputHelper');
?>
<nav aria-label="Website">
    <ul>
        <?php foreach ($websiteMenuItems as $menuItem) : ?>
            <?php $linkAttributes = $matchedRoute['id'] === $menuItem[0] ? ['class' => 'active'] : [] ?>
            <li><?= $helper->linkTo($menuItem[0], $linkAttributes, $menuItem[1]) ?></li>
        <?php endforeach ?>
    </ul>
</nav>
