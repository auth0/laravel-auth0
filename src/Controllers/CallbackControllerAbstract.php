<?php

declare(strict_types=1);

namespace Auth0\Laravel\Controllers;

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Entities\CredentialEntityContract;
use Auth0\Laravel\Events\{AuthenticationFailed, AuthenticationSucceeded};
use Auth0\Laravel\Exceptions\ControllerException;
use Auth0\Laravel\Exceptions\Controllers\CallbackControllerException;
use Auth0\Laravel\Guards\GuardAbstract;
use Auth0\Laravel\{Configuration, Events};
use Illuminate\Auth\Events\{Attempting, Authenticated, Failed, Validated};
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

use function is_string;

/**
 * @api
 */
abstract class CallbackControllerAbstract extends ControllerAbstract
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

        if (! $guard instanceof GuardAbstract) {
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

        /**
         * @var null|string $code
         * @var null|string $state
         */
        try {
            if (null !== $code && null !== $state) {
                Events::framework(new Attempting($guard::class, ['code' => $code, 'state' => $state], true));

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

            Events::framework(new Failed($guard::class, $guard->user(), $credentials));

            session()->invalidate();

            Events::dispatch($event = new AuthenticationFailed($throwable, true));

            if ($event->throwException) {
                throw $throwable;
            }
        }

        if (null !== $request->query('error') && null !== $request->query('error_description')) {
            // Workaround to aid static analysis, due to the mixed formatting of the query() response:
            $error = $request->query('error', '');
            $errorDescription = $request->query('error_description', '');
            $error = is_string($error) ? $error : '';
            $errorDescription = is_string($errorDescription) ? $errorDescription : '';

            Events::framework(new Attempting($guard::class, ['code' => $code, 'state' => $state], true));

            Events::framework(new Failed($guard::class, $guard->user(), [
                'code' => $code,
                'state' => $state,
                'error' => ['error' => $error, 'description' => $errorDescription],
            ]));

            session()->invalidate();

            // Create a dynamic exception to report the API error response
            $exception = new CallbackControllerException(sprintf(CallbackControllerException::MSG_API_RESPONSE, $error, $errorDescription));

            // Store the API exception in the session as a flash variable, in case the application wants to access it.
            session()->flash('auth0.callback.error', sprintf(CallbackControllerException::MSG_API_RESPONSE, $error, $errorDescription));

            Events::dispatch($event = new AuthenticationFailed($exception, true));

            if ($event->throwException) {
                throw $exception;
            }
        }

        if (! $success) {
            return redirect()->intended(config(Configuration::CONFIG_NAMESPACE_ROUTES . Configuration::CONFIG_ROUTE_LOGIN, '/login'));
        }

        $credential = ($guard instanceof Guard) ? $guard->find(Guard::SOURCE_SESSION) : $guard->find();

        $user = $credential?->getUser();

        if ($credential instanceof CredentialEntityContract && $user instanceof Authenticatable) {
            Events::framework(new Validated($guard::class, $user));

            session()->regenerate(true);

            /**
             * @var Guard $guard
             */
            $guard->login($credential, Guard::SOURCE_SESSION);

            Events::dispatch(new AuthenticationSucceeded($user));

            // @phpstan-ignore-next-line
            if ($user instanceof Authenticatable) {
                Events::framework(new Authenticated($guard::class, $user));
            }
        }

        return redirect()->intended(config(Configuration::CONFIG_NAMESPACE_ROUTES . Configuration::CONFIG_ROUTE_AFTER_LOGIN, config(Configuration::CONFIG_NAMESPACE_ROUTES . Configuration::CONFIG_ROUTE_INDEX, '/')));
    }
}
