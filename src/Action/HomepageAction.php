<?php

declare(strict_types=1);

namespace Miniblog\Engine\Action;

use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\HttpResponse;
use Miniblog\Engine\ArticleManager;

class HomepageAction extends AbstractAction
{
    public function __invoke(HttpRequest $request): HttpResponse
    {
        /** @var ArticleManager */
        $articleManager = $this->getServices()->get('articleManager');

        return $this->render('homepage_action/default.html.php', [
            'articles' => $articleManager->getRepository('BlogPost')->findAll(),
        ]);
    }
}
