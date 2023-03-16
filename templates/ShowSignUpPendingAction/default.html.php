<?php

use Miniblog\Engine\Schema\Thing\CreativeWork\WebSite;
use Miniblog\Engine\Schema\Thing\Person;

/** @var WebSite */
$website = $input['website'];
/** @var Person */
$owner = $input['owner'];

$title = 'Subscription Pending';

$output->insertInto('layout.html.php', 'mainContent', [
    'website' => $website,
    'owner' => $owner,
    'metaTitle' => $title,
    'showSignUpForm' => false,
]);
?>
<h1><?= $title ?></h1>

<p>Thank you.  We just sent you an email containing a link to confirm your subscription.  If it doesn't arrive shortly then please check your spam folder.</p>
