<?php

$title = 'Subscribe for Updates';

$output->insertInto('layout.html.php', 'mainContent', [
    'website' => $input['website'],
    'owner' => $input['owner'],
    'metaTitle' => $title,
    'metaDescription' => 'You can join our mailing list to receive updates by email',
    'showSignUpForm' => false,
]);
?>
<h1><?= $title ?></h1>

<?= $output->include('SignUpAction/form.html.php', [
    'values' => ($input['values'] ?? []),
    'errors' => ($input['errors'] ?? []),
]) ?>
