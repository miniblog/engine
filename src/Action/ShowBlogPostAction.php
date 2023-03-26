<?php

declare(strict_types=1);

namespace Miniblog\Engine\Action;

use DanBettles\Marigold\Exception\HttpException\NotFoundHttpException;
use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\HttpResponse;
use Miniblog\Engine\AbstractAction;
use Miniblog\Engine\Schema\Thing\CreativeWork\Article\SocialMediaPosting\BlogPosting;
use Miniblog\Engine\ThingManager;

use const null;

class ShowBlogPostAction extends AbstractAction
{
    /**
     * @throws NotFoundHttpException If the blog post does not exist
     */
    public function __invoke(HttpRequest $request): HttpResponse
    {
        /** @phpstan-var MatchedRoute */
        $matchedRoute = $request->attributes['route'];
        $postingId = $matchedRoute['parameters']['postingId'];

        /** @var ThingManager */
        $thingManager = $this->getServices()->get('thingManager');
        $blogPosting = $thingManager->find(BlogPosting::class, $postingId);

        if (null === $blogPosting) {
            throw new NotFoundHttpException("Blog post `{$postingId}`");
        }

        return $this->renderDefault([
            'website' => $thingManager->getThisWebsite(),
            'author' => $thingManager->getOwnerOfThisWebsite(),
            'blogPosting' => $blogPosting,
        ]);
    }
}
