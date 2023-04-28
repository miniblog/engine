<?php

declare(strict_types=1);

namespace Miniblog\Engine\Action;

use DanBettles\Marigold\Exception\HttpException;
use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\HttpResponse;
use Miniblog\Engine\AbstractAction;
use Miniblog\Engine\Schema\Thing\CreativeWork\Article;
use Miniblog\Engine\ThingManager;
use RuntimeException;

use function array_key_exists;

use const null;

class ShowArticleAction extends AbstractAction
{
    /**
     * @throws RuntimeException If the `id` route-parameter is missing
     * @throws HttpException If the article does not exist
     */
    public function __invoke(HttpRequest $request): HttpResponse
    {
        /** @phpstan-var MatchedRoute */
        $matchedRoute = $request->attributes['route'];

        if (!array_key_exists('id', $matchedRoute['parameters'])) {
            throw new RuntimeException('The `id` route-parameter is missing');
        }

        $articleId = $matchedRoute['parameters']['id'];
        /** @var ThingManager */
        $thingManager = $this->getServices()->get('thingManager');
        /** @var Article|null */
        $article = $thingManager->find(Article::class, $articleId);

        $this->abortGracefullyIf(
            null === $article,
            HttpResponse::HTTP_NOT_FOUND,
            "Article `{$articleId}`"
        );

        return $this->renderDefault([
            'author' => $thingManager->getOwnerOfThisWebsite(),
            'article' => $article,
        ]);
    }
}
