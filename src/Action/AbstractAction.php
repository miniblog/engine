<?php

declare(strict_types=1);

namespace Miniblog\Engine\Action;

use DanBettles\Marigold\AbstractAction as MarigoldAbstractAction;
use DanBettles\Marigold\Registry;
use DanBettles\Marigold\TemplateEngine\Engine;

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
