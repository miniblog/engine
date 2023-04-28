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

/** @var Router $router */
$router = $globals->get('router');
/** @var OutputHelper */
$helper = $globals->get('outputHelper');
?>
<article class="sign-up">
    <header>
        <h1><?= $title ?></h1>
        <h2>Join our mailing list to receive updates by email</h2>
    </header>

    <form method="POST" action="<?= $router->generatePath('signUp') ?>">
        <p>
            <?php $errorMessage = $errors['email'] ?>
            <?php $helpId = $helper->createUniqueName() ?>

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

            <span class="<?= $errorMessage ? 'invalid-feedback' : 'form-help' ?>" id="<?= $helpId ?>"><small>
                <?= $errorMessage ? $helper->escape($errorMessage) : "We'll never share your email" ?>
            </small></span>
        </p>

        <p>
            <button type="submit">Sign Up</button>
        </p>
    </form>
</article>
