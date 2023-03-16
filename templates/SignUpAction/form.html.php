<?php

use DanBettles\Marigold\Router;
use Miniblog\Engine\OutputHelper;

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

/** @var Router */
$router = $globals->get('router');
/** @var OutputHelper */
$helper = $globals->get('outputHelper');
?>
<div class="sign-up">
    <p>Join our mailing list to receive updates by email</p>

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
