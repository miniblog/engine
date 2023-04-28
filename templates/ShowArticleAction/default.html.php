<?php

use Miniblog\Engine\Schema\Thing\CreativeWork\Article;
use Miniblog\Engine\Schema\Thing\CreativeWork\WebSite;
use Miniblog\Engine\Schema\Thing\Person;

/** @var WebSite */
$website = $input['website'];
/** @var Person */
$author = $input['author'];
/** @var Article */
$article = $input['article'];

$output->insertInto('layout.html.php', 'mainContent', [
    'website' => $input['website'],
    'owner' => $input['owner'],
    'metaTitle' => $article->getHeadline(),
    'metaDescription' => $article->getDescription(),
]);

echo $output->include('article.html.php', [
    'website' => $website,
    'author' => $author,
    'article' => $article,
    'itemType' => 'https://schema.org/Article',
]);
