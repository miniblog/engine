<?php

declare(strict_types=1);

namespace Miniblog\Engine\Action;

use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\HttpResponse;
use Miniblog\Engine\AbstractAction;

class ShowSignUpCompleteAction extends AbstractAction
{
    public function __invoke(HttpRequest $request): HttpResponse
    {
        return $this->renderDefault();
    }
}
