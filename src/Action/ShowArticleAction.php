<?php

declare(strict_types=1);

namespace Miniblog\Engine\Action;

use DanBettles\Marigold\Exception\HttpException\NotFoundHttpException;
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
     * @throws NotFoundHttpException If the article does not exist
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

        if (null === $article) {
            throw new NotFoundHttpException("Article `{$articleId}`");
        }

        return $this->renderDefault([
            'website' => $thingManager->getThisWebsite(),
            'owner' => $thingManager->getOwnerOfThisWebsite(),
            'article' => $article,
        ]);
    }
}
