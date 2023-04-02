<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\AbstractAction as MarigoldAbstractAction;
use DanBettles\Marigold\Exception\HttpException;
use DanBettles\Marigold\HttpResponse;
use DanBettles\Marigold\HttpResponse\RedirectHttpResponse;
use DanBettles\Marigold\Registry;
use DanBettles\Marigold\Router;
use DanBettles\Marigold\TemplateEngine\Engine;
use ReflectionClass;

use function array_replace;

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
     * Makes clear the distinction between throwing an *HTTP exception* in response to something the user did, which
     * will result in an error page, and throwing an exception in response to a program error.
     *
     * @throws HttpException If the condition is met
     */
    public function abortGracefullyIf(
        bool $condition,
        int $statusCode,
        string $message = ''
    ): void {
        if ($condition) {
            throw new HttpException($statusCode, $message);
        }
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

        /** @var ThingManager */
        $thingManager = $this->getServices()->get('thingManager');

        $variables = array_replace([
            'website' => $thingManager->getThisWebsite(),
            'owner' => $thingManager->getOwnerOfThisWebsite(),
        ], $variables);

        return $this->render(
            "{$shortClassName}/default.html.php",
            $variables,
            $httpStatusCode
        );
    }

    /**
     * @param array<string,string|int> $parameters
     */
    protected function redirectToRoute(
        string $routeId,
        array $parameters = [],
        int $statusCode = HttpResponse::HTTP_FOUND
    ): RedirectHttpResponse {
        /** @var Router */
        $router = $this->getServices()->get('router');
        $path = $router->generatePath($routeId, $parameters);

        return new RedirectHttpResponse($path, $statusCode);
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
