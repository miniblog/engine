<?php

declare(strict_types=1);

namespace Miniblog\Engine\Action;

use DanBettles\Marigold\Exception\HttpException;
use DanBettles\Marigold\HttpRequest;
use DanBettles\Marigold\HttpResponse;
use Miniblog\Engine\AbstractAction;
use RuntimeException;
use Throwable;

use function fclose;
use function fgetcsv;
use function filter_var;
use function fopen;
use function fputcsv;
use function rewind;
use function strtolower;

use const false;
use const FILTER_VALIDATE_EMAIL;
use const true;

class AddSubscriberAction extends AbstractAction
{
    /**
     * @throws HttpException If the request method is invalid
     * @throws HttpException If the email address is invalid
     * @throws RuntimeException If it failed to open the subscribers file
     * @throws RuntimeException If it failed to rewind the subscribers file
     * @throws RuntimeException If it failed to append to the subscribers file
     */
    public function __invoke(HttpRequest $request): HttpResponse
    {
        $this->abortGracefullyIf(
            'POST' !== $request->server['REQUEST_METHOD'],
            HttpResponse::HTTP_BAD_REQUEST,
            'The request method is invalid'
        );

        $email = $request->request['email'];

        // The email address *should* be valid at this stage.
        $this->abortGracefullyIf(
            false === filter_var($email, FILTER_VALIDATE_EMAIL),
            HttpResponse::HTTP_BAD_REQUEST,
            'The email address is invalid'
        );

        /** @phpstan-var ConfigArray */
        $config = $this->getServices()->get('config');

        // N.B. Always append.
        $subscribersFile = fopen($config['dataDir'] . '/subscribers.csv', 'a+');

        if (false === $subscribersFile) {
            throw new RuntimeException('Failed to open the subscribers file');
        }

        if (!rewind($subscribersFile)) {
            throw new RuntimeException('Failed to rewind the subscribers file');
        }

        $newEmailLowercase = strtolower($email);
        $newEmailExists = false;

        try {
            while (false !== (/** @phpstan-var array<int,string> */$data = fgetcsv($subscribersFile, 0))) {
                /** @var string|null */
                $currentEmail = $data[0];

                if (null !== $currentEmail && $newEmailLowercase === strtolower($currentEmail)) {
                    $newEmailExists = true;
                    break;
                }
            }
        } catch (Throwable $throwable) {
            @fclose($subscribersFile);
            throw $throwable;
        }

        if (!$newEmailExists) {
            if (false === fputcsv($subscribersFile, [$email])) {
                throw new RuntimeException('Failed to append to the subscribers file');
            }

            @fclose($subscribersFile);
        }

        return $this->redirectToRoute('showSignUpComplete', [], HttpResponse::HTTP_SEE_OTHER);
    }
}
