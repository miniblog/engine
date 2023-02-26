<?php

/**
 * @param WebSite website
 */

use Miniblog\Engine\Schema\Thing\CreativeWork\WebSite;

$output->insertInto('show_error_action/layout.html.php', 'mainContent', [
    'website' => $input['website'],
    'metaTitle' => 'Unexpected Error',
]);
?>
All of a sudden, a terrifying shriek and then...  Darkness.  Alas, your quest is over.  For now.
