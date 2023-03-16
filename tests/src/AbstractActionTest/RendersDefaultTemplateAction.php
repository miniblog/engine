<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests\AbstractActionTest;

use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\HttpResponse;
use Miniblog\Engine\AbstractAction;

class RendersDefaultTemplateAction extends AbstractAction
{
    public function __invoke(HttpRequest $request): HttpResponse
    {
        return $this->renderDefault([
            'message' => '404 Not Found',
        ], 404);
    }
}
