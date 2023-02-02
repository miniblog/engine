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

        return $this->renderDefault([
            'blurb' => $articleManager->getRepository('Article')->find('blurb'),
            'articles' => $articleManager->getRepository('BlogPost')->findAll(),
        ]);
    }
}
