<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests\Action\AbstractActionTest;

use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\HttpResponse;
use Miniblog\Engine\Action\AbstractAction;

class RendersDefaultTemplateAction extends AbstractAction
{
    public function __invoke(HttpRequest $request): HttpResponse
    {
        return $this->renderDefault([
            'message' => '404 Not Found',
        ], HttpResponse::HTTP_NOT_FOUND);
    }
}
