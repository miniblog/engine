<?php

declare(strict_types=1);

namespace Miniblog\Engine\Action;

use DanBettles\Marigold\Exception\HttpException;
use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\HttpResponse;
use Miniblog\Engine\AbstractAction;
use Miniblog\Engine\ThingManager;
use RuntimeException;

use function filter_var;
use function mail;

use const false;
use const FILTER_VALIDATE_EMAIL;

class SignUpAction extends AbstractAction
{
    /**
     * @throws HttpException If the request method is invalid
     */
    public function __invoke(HttpRequest $request): HttpResponse
    {
        if ('GET' === $request->server['REQUEST_METHOD']) {
            // Show the (empty) sign-up form.
            return $this->renderDefault();
        }

        if ('POST' !== $request->server['REQUEST_METHOD']) {
            throw new HttpException(HttpResponse::HTTP_BAD_REQUEST, 'The request method is invalid');
        }

        $submittedValues = [
            'email' => $request->request['email'] ?? '',
        ];

        $errors = [];

        if (false === filter_var($submittedValues['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'This email address is invalid';
        }

        if ($errors) {
            return $this->renderDefault([
                'values' => $submittedValues,
                'errors' => $errors,
            ], HttpResponse::HTTP_UNPROCESSABLE_CONTENT);
        }

        /** @phpstan-var ConfigArray */
        $config = $this->getServices()->get('config');

        if ('dev' === $config['env']) {
            return $this->redirectToRoute('showSignUpConfirmationEmail', [], HttpResponse::HTTP_SEE_OTHER);
        }

        $this->sendConfirmationEmail($submittedValues);

        return $this->redirectToRoute('showSignUpPending', [], HttpResponse::HTTP_SEE_OTHER);
    }

    /**
     * @param array<string,string>|string $additionalHeaders
     */
    protected function mailProxy(
        string $to,
        string $subject,
        string $message,
        $additionalHeaders = [],
        string $additionalParams = ''
    ): bool {
        return mail($to, $subject, $message, $additionalHeaders, $additionalParams);
    }

    /**
     * @param array<string,string> $submittedValues
     * @throws RuntimeException If it failed to send a confirmation email
     */
    private function sendConfirmationEmail(array $submittedValues): void
    {
        /** @var ThingManager */
        $thingManager = $this->getServices()->get('thingManager');
        $website = $thingManager->getThisWebsite();
        $owner = $thingManager->getOwnerOfThisWebsite();

        $to = $submittedValues['email'];
        $subject = 'Please confirm your email address';

        $message = $this->getTemplateEngine()->render('SignUpAction/confirmation_email.html.php', [
            'website' => $website,
            'owner' => $owner,
            'subject' => $subject,
            'email' => $to,  // These must be the same.
        ]);

        $from = "{$website->getHeadline()} <{$owner->getEmail()}>";

        $sent = $this->mailProxy($to, $subject, $message, [
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/html; charset=utf-8',
            'From' => $from,
            'Reply-To' => $from,
        ]);

        if (!$sent) {
            throw new RuntimeException("Failed to send a confirmation email to `{$to}`");
        }
    }
}
