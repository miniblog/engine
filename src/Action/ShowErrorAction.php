<?php

declare(strict_types=1);

namespace Miniblog\Engine\Action;

use DanBettles\Marigold\Exception\HttpException;
use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\HttpResponse;
use Miniblog\Engine\AbstractAction;
use Miniblog\Engine\ErrorsService;
use Throwable;

use function array_key_exists;
use function file_get_contents;

class ShowErrorAction extends AbstractAction
{
    public function __invoke(HttpRequest $request): HttpResponse
    {
        if (!array_key_exists('error', $request->attributes)) {
            // For now, this is *our* problem.
            return new HttpResponse('The error is missing from the request.', HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        $error = $request->attributes['error'];

        if (!$error instanceof Throwable) {
            // For now, this is *our* problem.
            return new HttpResponse('The error is not a throwable.', HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        /** @var array{env:string} */
        $config = $this->getServices()->get('config');

        /** @var ErrorsService */
        $errorsService = $this->getServices()->get('errorsService');

        $statusCode = HttpResponse::HTTP_INTERNAL_SERVER_ERROR;

        if ($error instanceof HttpException) {
            /** @var HttpException $error */
            $statusCode = $error->getStatusCode();
        }

        $responseContent = "<pre>{$error}</pre>";

        if ('prod' === $config['env']) {
            /** @var string */
            $responseContent = file_get_contents($errorsService->getPagePathname($statusCode));
        }

        return new HttpResponse($responseContent, $statusCode);
    }
}
