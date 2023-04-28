<?php

use Miniblog\Engine\Schema\Thing\CreativeWork\Article\SocialMediaPosting\BlogPosting;
use Miniblog\Engine\Schema\Thing\CreativeWork\WebSite;
use Miniblog\Engine\Schema\Thing\Person;

/** @var WebSite */
$website = $input['website'];
/** @var Person */
$author = $input['author'];
/** @var BlogPosting */
$blogPosting = $input['blogPosting'];

$output->insertInto('layout.html.php', 'mainContent', [
    'website' => $website,
    'owner' => $author,
    'metaTitle' => $blogPosting->getHeadline(),
    'metaDescription' => $blogPosting->getDescription(),
]);

echo $output->include('article.html.php', [
    'website' => $website,
    'author' => $author,
    'article' => $blogPosting,
    'itemType' => 'https://schema.org/BlogPosting',
]);
