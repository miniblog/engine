<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\Exception\HttpException;
use DanBettles\Marigold\Exception\NotFoundHttpException;
use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\HttpResponse;
use DanBettles\Marigold\Registry;
use DanBettles\Marigold\Router;
use DanBettles\Marigold\TemplateEngine\Engine;
use DanBettles\Marigold\TemplateEngine\TemplateFileLoader;
use Miniblog\Engine\Action\AbstractAction;
use Throwable;

use const null;

class Miniblog
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->setRegistry($registry);
    }

    // @todo Create some kind of error-handler class from this.
    private function handleError(Throwable $throwable): void
    {
        $httpExceptionWasThrown = $throwable instanceof HttpException;

        $statusCode = HttpResponse::HTTP_INTERNAL_SERVER_ERROR;

        if ($httpExceptionWasThrown) {
            /** @var HttpException $throwable */
            $statusCode = $throwable->getStatusCode();
        }

        $registry = $this->getRegistry();

        /** @var array<string,mixed> */
        $config = $registry->get('config');

        $content = '';

        if ('dev' === $config['env']) {
            $content = "<pre>{$throwable}</pre>";
        } else {
            /** @var TemplateFileLoader */
            $templateFileLoader = $registry->get('templateFileLoader');

            $templateFile = $httpExceptionWasThrown
                ? $templateFileLoader->findTemplate("_errors/error_{$statusCode}.html.php")
                : null
            ;

            /** @var Engine */
            $templateEngine = $registry->get('templateEngine');

            $content = $templateEngine->render(($templateFile ?: "_errors/error.html.php"), [
                'throwable' => $throwable,
            ]);
        }

        /** @var HttpRequest */
        $request = $registry->get('request');

        (new HttpResponse($content, $statusCode))->send($request);
    }

    public function handleRequest(): void
    {
        try {
            $registry = $this->getRegistry();

            /** @var HttpRequest */
            $request = $registry->get('request');

            /** @var Router */
            $router = $registry->get('router');
            $matchedRoute = $router->match($request);

            if (null === $matchedRoute) {
                throw new NotFoundHttpException();
            }

            $request->attributes['route'] = $matchedRoute;

            /** @var Engine */
            $templateEngine = $registry->get('templateEngine');

            /** @phpstan-var class-string<AbstractAction> */
            $actionClassName = $matchedRoute['action'];
            /** @var AbstractAction */
            $action = new $actionClassName($templateEngine, $registry);
            $response = $action($request);

            $response->send($request);
        } catch (Throwable $t) {
            $this->handleError($t);
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
}
