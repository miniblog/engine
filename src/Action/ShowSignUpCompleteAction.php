<?php

declare(strict_types=1);

namespace Miniblog\Engine\Action;

use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\HttpResponse;
use Miniblog\Engine\AbstractAction;
use Miniblog\Engine\ThingManager;

class ShowSignUpCompleteAction extends AbstractAction
{
    public function __invoke(HttpRequest $request): HttpResponse
    {
        /** @var ThingManager */
        $thingManager = $this->getServices()->get('thingManager');

        return $this->renderDefault([
            'website' => $thingManager->getThisWebsite(),
            'owner' => $thingManager->getOwnerOfThisWebsite(),
        ]);
    }
}
