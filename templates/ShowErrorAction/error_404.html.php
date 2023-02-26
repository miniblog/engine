<?php

/**
 * @param WebSite website
 */

use Miniblog\Engine\Schema\Thing\CreativeWork\WebSite;

$output->insertInto('ShowErrorAction/layout.html.php', 'mainContent', [
    'website' => $input['website'],
    'metaTitle' => 'Not Found',
]);
?>
I would have thought twice about following that path.  The ground disappears from beneath your feet and you are left staring into the abyss.
