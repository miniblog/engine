<?php

use DanBettles\Marigold\Router;
use Miniblog\Engine\OutputHelper;

$title = 'Subscribe for Updates';

$output->insertInto('layout.html.php', 'mainContent', [
    'website' => $input['website'],
    'owner' => $input['owner'],
    'metaTitle' => $title,
    'metaDescription' => 'You can join our mailing list to receive updates by email',
]);

// For convenience.
/** @var array<string,string> */
$values = array_replace([
    'email' => '',
], ($input['values'] ?? []));

// For convenience.
/** @var array<string,string> */
$errors = array_replace([
    'email' => '',
], ($input['errors'] ?? []));

/** @var OutputHelper */
$helper = $globals->get('outputHelper');
?>
<h1><?= $title ?></h1>

<div class="sign-up">
    <p>Join our mailing list to receive updates by email</p>

    <?php /** @var Router $router */ $router = $globals->get('router') ?>
    <form
        method="POST"
        action="<?= $router->generatePath('signUp') ?>"
    >
        <div>
            <?php $errorMessage = $errors['email'] ?>
            <?php $helpId = uniqid() ?>

            <input
                type="email"
                name="email"
                value="<?= $helper->escape($values['email']) ?>"
                required
                placeholder="Your email"
                class="<?= $errorMessage ? 'is-invalid' : '' ?>"
                aria-label="Email"
                aria-describedby="<?= $helpId ?>"
                data-lpignore="true"
            >

            <p class="<?= $errorMessage ? 'invalid-feedback' : 'form-help' ?>" id="<?= $helpId ?>">
                <?= $errorMessage ? $helper->escape($errorMessage) : "We'll never share your email" ?>
            </p>
        </div>

        <div>
            <button type="submit">Sign Up</button>
        </div>
    </form>
</div>
