<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\AbstractAction as MarigoldAbstractAction;
use DanBettles\Marigold\HttpResponse;
use DanBettles\Marigold\Registry;
use DanBettles\Marigold\TemplateEngine\Engine;
use ReflectionClass;

abstract class AbstractAction extends MarigoldAbstractAction
{
    private Registry $services;

    public function __construct(
        Engine $templateEngine,
        Registry $services
    ) {
        $this->setServices($services);

        parent::__construct($templateEngine);
    }

    /**
     * Renders the default template for this action.
     *
     * For the action class `DoSomethingAction`, for example, this would be `"DoSomethingAction/default.html.php"`.
     *
     * @param array<string,mixed> $variables
     */
    protected function renderDefault(
        array $variables = [],
        int $httpStatusCode = HttpResponse::HTTP_OK
    ): HttpResponse {
        $shortClassName = (new ReflectionClass($this))->getShortName();

        return $this->render(
            "{$shortClassName}/default.html.php",
            $variables,
            $httpStatusCode
        );
    }

    private function setServices(Registry $services): self
    {
        $this->services = $services;
        return $this;
    }

    public function getServices(): Registry
    {
        return $this->services;
    }
}
