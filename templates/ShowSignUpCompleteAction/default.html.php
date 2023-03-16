<?php

use Miniblog\Engine\OutputHelper;
use Miniblog\Engine\Schema\Thing\CreativeWork\WebSite;
use Miniblog\Engine\Schema\Thing\Person;

/** @var WebSite */
$website = $input['website'];
/** @var Person */
$owner = $input['owner'];

$title = 'Sign Up Complete';

$output->insertInto('layout.html.php', 'mainContent', [
    'website' => $website,
    'owner' => $owner,
    'metaTitle' => $title,
    'showSignUpForm' => false,
]);

/** @var OutputHelper */
$helper = $globals->get('outputHelper');
?>
<h1><?= $title ?></h1>

<p>Thank you for confirming your email address.  You've been added to our mailing list.</p>

<p>If you'd like to be removed at any time then please <?= $helper->linkTo("mailto:{$owner->getEmail()}", 'drop us a line') ?> or reply to one of our mailings.</p>
