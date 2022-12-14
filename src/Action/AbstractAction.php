<?php

declare(strict_types=1);

namespace Miniblog\Engine\Action;

use DanBettles\Marigold\AbstractAction as MarigoldAbstractAction;
use DanBettles\Marigold\HttpResponse;
use DanBettles\Marigold\Registry;
use DanBettles\Marigold\TemplateEngine\Engine;
use ReflectionClass;

use function ltrim;
use function preg_replace;
use function strtolower;
use function str_replace;

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
     * For the action class `DoSomethingAction`, for example, this would be `"do_something_action/default.html.php"`.
     *
     * @param array<string,mixed> $variables
     */
    protected function renderDefault(
        array $variables = [],
        int $httpStatusCode = HttpResponse::HTTP_OK
    ): HttpResponse {
        $shortClassName = (new ReflectionClass($this))->getShortName();
        /** @var string */
        $spaced = preg_replace('~([A-Z])~', ' $1', $shortClassName);
        $underscored = strtolower(str_replace(' ', '_', ltrim($spaced)));

        return $this->render(
            "{$underscored}/default.html.php",
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
