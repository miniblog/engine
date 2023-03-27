<?php

use DanBettles\Marigold\Router;
use Miniblog\Engine\OutputHelper;
use Miniblog\Engine\Schema\Thing\CreativeWork\WebSite;
use Miniblog\Engine\Schema\Thing\Person;

/** @var WebSite */
$website = $input['website'];
/** @var Person */
$owner = $input['owner'];
/** @var string */
$subject = $input['subject'];
/** @var string */
$email = $input['email'];

/** @var OutputHelper */
$helper = $globals->get('outputHelper');
/** @var Router */
$router = $globals->get('router');

$titleStyle = 'font-family: Arial, sans-serif; font-size: 18px';
$bodyTextStyle = 'font-family: Georgia, serif; font-size: 18px';
/** @var string */
$websiteName = $website->getHeadline();
$websiteLink = $helper->createA(['href' => $website->getUrl()], $helper->escape($websiteName));
/** @var string */
$websiteLang = $website->getInLanguage();
/** @var string */
$ownerName = $owner->getFullName();
?>
<!doctype html>
<html lang="<?= $helper->escape($websiteLang) ?>">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?= $helper->escape($subject) ?></title>
    </head>

    <body style="line-height: 1.6; background-color: #f2f2f2; margin: 0; padding: 21px">
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td valign="top" align="center">

                    <table width="600" cellspacing="0" cellpadding="21" bgcolor="#ffffff">
                        <tr>
                            <td>

                                <p style="<?= "{$titleStyle}; font-size: 1.5em" ?>">Hi 👋</p>

                                <p style="<?= $bodyTextStyle ?>">Your email address was just used to join the <?= $websiteLink ?> mailing list.  If that was you then please click the button to start your subscription.</p>

                                <form method="POST" action="<?= $router->generatePath('addSubscriber') ?>">
                                    <input type="hidden" name="email" value="<?= $helper->escape($email) ?>">
                                    <button type="submit" style="<?= $titleStyle ?>">Yes, I'd like to join your mailing list</button>
                                </form>

                                <p style="<?= $bodyTextStyle ?>">Otherwise you can just delete this message.  Sorry if we bothered you.</p>

                                <p style="<?= $bodyTextStyle ?>">
                                    Best,<br>
                                    <span style="font-style: italic"><?= $helper->escape($ownerName) ?></span><br>
                                    Owner, <?= $websiteLink ?>
                                </p>

                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>
    </body>
</html>
