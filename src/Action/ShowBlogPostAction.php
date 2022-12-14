<?php

declare(strict_types=1);

namespace Miniblog\Engine\Action;

use DanBettles\Marigold\Exception\NotFoundHttpException;
use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\HttpResponse;
use Miniblog\Engine\ArticleManager;

use const null;

class ShowBlogPostAction extends AbstractAction
{
    /**
     * @throws NotFoundHttpException If the blog post does not exist.
     */
    public function __invoke(HttpRequest $request): HttpResponse
    {
        /** @var array{parameters: array<string,string>} */
        $matchedRoute = $request->attributes['route'];
        $postId = $matchedRoute['parameters']['postId'];

        /** @var ArticleManager */
        $articleManager = $this->getServices()->get('articleManager');
        $article = $articleManager->getRepository('BlogPost')->find($postId);

        if (null === $article) {
            throw new NotFoundHttpException("Blog post `{$postId}`");
        }

        return $this->render('show_blog_post_action/default.html.php', [
            'article' => $article,
        ]);
    }
}
