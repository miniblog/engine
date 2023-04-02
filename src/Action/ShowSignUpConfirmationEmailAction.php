<?php

declare(strict_types=1);

namespace Miniblog\Engine\Action;

use DanBettles\Marigold\Exception\HttpException;
use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\HttpResponse;
use Miniblog\Engine\AbstractAction;
use Miniblog\Engine\ThingManager;

class ShowSignUpConfirmationEmailAction extends AbstractAction
{
    /**
     * @throws HttpException If the user is not permitted to view the page.
     */
    public function __invoke(HttpRequest $request): HttpResponse
    {
        /** @phpstan-var ConfigArray */
        $config = $this->getServices()->get('config');

        $this->abortGracefullyIf(
            'dev' !== $config['env'],
            HttpResponse::HTTP_FORBIDDEN
        );

        /** @var ThingManager */
        $thingManager = $this->getServices()->get('thingManager');
        $owner = $thingManager->getOwnerOfThisWebsite();

        return $this->render('SignUpAction/confirmation_email.html.php', [
            'website' => $thingManager->getThisWebsite(),
            'owner' => $owner,
            'subject' => 'Email Subject',
            'email' => $owner->getEmail(),
        ]);
    }
}
