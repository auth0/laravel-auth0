<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller\Stateful;

use Auth0\Laravel\Guards\AuthenticationGuardContract;
use Auth0\Laravel\Entities\CredentialEntityContract;
use Auth0\Laravel\Event\Stateful\{AuthenticationFailed, AuthenticationSucceeded};
use Auth0\Laravel\Exception\ControllerException;
use Auth0\Laravel\Exception\Stateful\CallbackException;
use Auth0\Laravel\Http\Controller\ControllerAbstract;
use Illuminate\Auth\Events\{Attempting, Authenticated, Failed, Validated};
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

use function is_string;

/**
 * Controller for handling a callback request, after a user is returned from authenticating with Auth0.
 *
 * @api
 */
final class Callback extends ControllerAbstract implements CallbackContract
{
    /**
     * @psalm-suppress InvalidArgument
     *
     * @param Request $request
     */
    public function __invoke(
        Request $request,
    ): Response {
        $guard = auth()->guard();

        if (! $guard instanceof AuthenticationGuardContract) {
            logger()->error(sprintf('A request implementing the `%s` controller was not routed through a Guard configured with an Auth0 driver. The incorrectly assigned Guard was: %s', self::class, $guard::class), $request->toArray());

            throw new ControllerException(ControllerException::ROUTED_USING_INCOMPATIBLE_GUARD);
        }

        $code = $request->query('code');
        $state = $request->query('state');
        $code = is_string($code) ? trim($code) : '';
        $state = is_string($state) ? trim($state) : '';
        $success = false;

        if ('' === $code) {
            $code = null;
        }

        if ('' === $state) {
            $state = null;
        }

        /*
         * @var string|null $code
         * @var string|null $state
         */

        try {
            if (null !== $code && null !== $state) {
                event(new Attempting($guard::class, ['code' => $code, 'state' => $state], true));

                $success = $guard->sdk()->exchange(
                    code: $code,
                    state: $state,
                );
            }
        } catch (Throwable $throwable) {
            $credentials = $guard->sdk()->getUser() ?? [];
            $credentials['code'] = $code;
            $credentials['state'] = $state;
            $credentials['error'] = ['description' => $throwable->getMessage()];

            event(new Failed($guard::class, $guard->user(), $credentials));

            $guard->sdk()->clear();

            // Throw hookable $event to allow custom error handling scenarios.
            $event = new AuthenticationFailed($throwable, true);
            event($event);

            // If the event was not hooked by the application, throw an exception:
            if ($event->getThrowException()) {
                throw $throwable;
            }
        }

        if (null !== $request->query('error') && null !== $request->query('error_description')) {
            // Workaround to aid static analysis, due to the mixed formatting of the query() response:
            $error = $request->query('error', '');
            $errorDescription = $request->query('error_description', '');
            $error = is_string($error) ? $error : '';
            $errorDescription = is_string($errorDescription) ? $errorDescription : '';

            event(new Attempting($guard::class, ['code' => $code, 'state' => $state], true));

            event(new Failed($guard::class, $guard->user(), [
                'code' => $code,
                'state' => $state,
                'error' => ['error' => $error, 'description' => $errorDescription],
            ]));

            // Clear the local session via the Auth0-PHP SDK:
            $guard->sdk()->clear();

            // Create a dynamic exception to report the API error response
            $exception = new CallbackException(sprintf(CallbackException::MSG_API_RESPONSE, $error, $errorDescription));

            // Store the API exception in the session as a flash variable, in case the application wants to access it.
            session()->flash('auth0.callback.error', sprintf(CallbackException::MSG_API_RESPONSE, $error, $errorDescription));

            // Throw hookable $event to allow custom error handling scenarios:
            $event = new AuthenticationFailed($exception, true);
            event($event);

            // If the event was not hooked by the application, throw an exception:
            if ($event->getThrowException()) {
                throw $exception;
            }
        }

        if (! $success) {
            return redirect()->intended(config('auth0.routes.login', 'login'));
        }

        $credential = $guard->find(AuthenticationGuardContract::SOURCE_SESSION);
        $user = $credential?->getUser();

        if ($credential instanceof CredentialEntityContract && $user instanceof Authenticatable) {
            event(new Validated($guard::class, $user));
            $guard->login($credential, AuthenticationGuardContract::SOURCE_SESSION);

            $request->session()->regenerate();

            $event = new AuthenticationSucceeded($user);
            event($event);
            $user = $event->getUser();
            $guard->setUser($user);

            // @phpstan-ignore-next-line
            if ($user instanceof Authenticatable) {
                event(new Authenticated($guard::class, $user));
            }
        }

        return redirect()->intended(config('auth0.routes.home', '/'));
    }
}
