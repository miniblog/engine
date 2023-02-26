<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\Exception\NotFoundHttpException;
use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\Registry;
use DanBettles\Marigold\Router;
use DanBettles\Marigold\TemplateEngine\Engine;
use Miniblog\Engine\Action\ShowErrorAction;
use Throwable;

use const null;

class Website
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->setRegistry($registry);
    }

    /**
     * @param class-string<AbstractAction> $className
     */
    private function createAction(string $className): AbstractAction
    {
        $registry = $this->getRegistry();
        /** @var Engine */
        $templateEngine = $registry->get('templateEngine');
        $action = new $className($templateEngine, $registry);

        return $action;
    }

    public function handleRequest(): void
    {
        try {
            $request = $this->getRequest();

            /** @var Router */
            $router = $this->getRegistry()->get('router');
            /** @phpstan-var MatchedRoute|null */
            $matchedRoute = $router->match($request);

            if (null === $matchedRoute) {
                throw new NotFoundHttpException();
            }

            $request->attributes['route'] = $matchedRoute;

            $action = $this->createAction($matchedRoute['action']);
            $response = $action($request);
            $response->send($request);
        } catch (Throwable $t) {
            $request = $this->getRequest();
            // Same request: the error occurred *during* the request.
            $request->attributes['error'] = $t;

            $action = $this->createAction(ShowErrorAction::class);
            $response = $action($request);
            $response->send($request);
        }
    }

    private function setRegistry(Registry $registry): self
    {
        $this->registry = $registry;
        return $this;
    }

    private function getRegistry(): Registry
    {
        return $this->registry;
    }

    private function getRequest(): HttpRequest
    {
        /** @var HttpRequest */
        return $this->getRegistry()->get('request');
    }
}
