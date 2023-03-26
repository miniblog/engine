<?php

declare(strict_types=1);

namespace Miniblog\Engine\Action;

use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\HttpResponse;
use DateTime;
use Miniblog\Engine\AbstractAction;
use Miniblog\Engine\Schema\Thing\CreativeWork\Article\SocialMediaPosting\BlogPosting;
use Miniblog\Engine\ThingManager;

use function usort;

use const true;

class ShowHomepageAction extends AbstractAction
{
    public function __invoke(HttpRequest $request): HttpResponse
    {
        /** @var ThingManager */
        $thingManager = $this->getServices()->get('thingManager');

        /** @var BlogPosting[] */
        $blogPostings = $thingManager->findAll(BlogPosting::class);

        usort($blogPostings, function (BlogPosting $posting1, BlogPosting $posting2): int {
            /** @var DateTime */
            $publishedAt1 = $posting1->getDatePublished(true);
            /** @var DateTime */
            $publishedAt2 = $posting2->getDatePublished(true);

            return -1 * ($publishedAt1->getTimestamp() <=> $publishedAt2->getTimestamp());
        });

        return $this->renderDefault([
            'blogPostings' => $blogPostings,
        ]);
    }
}
