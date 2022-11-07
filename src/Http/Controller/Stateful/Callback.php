<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller\Stateful;

use Auth0\Laravel\Contract\Auth\Guard;

final class Callback implements \Auth0\Laravel\Contract\Http\Controller\Stateful\Callback
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        /**
         * @var \Illuminate\Contracts\Auth\Factory $auth
         */
        $auth = auth();

        /**
         * @var Guard $guard
         */
        $guard = $auth->guard('auth0');

        // Check if the user already has a session:
        if ($guard->check()) {
            // They do; redirect to homepage.
            return redirect()->intended(config('auth0.routes.home', '/')); // @phpstan-ignore-line
        }

        $code = $request->query('code');
        $state = $request->query('state');

        if (! \is_string($code) || '' === $code) {
            $code = null;
        }

        if (! \is_string($state) || '' === $state) {
            $state = null;
        }

        /*
         * @var string|null $code
         * @var string|null $state
         */

        try {
            if (null !== $code && null !== $state) {
                event(new \Illuminate\Auth\Events\Attempting($guard::class, ['code' => $code, 'state' => $state], true));

                app(\Auth0\Laravel\Auth0::class)->getSdk()->exchange(
                    code: $code,
                    state: $state,
                );
            }
        } catch (\Throwable $exception) {
            $credentials = app(\Auth0\Laravel\Auth0::class)->getSdk()->getUser() ?? [];
            $credentials['code'] = $code;
            $credentials['state'] = $state;
            $credentials['error'] = ['description' => $exception->getMessage()];

            event(new \Illuminate\Auth\Events\Failed($guard::class, $guard->user(), $credentials));

            app(\Auth0\Laravel\Auth0::class)->getSdk()->clear();

            // Throw hookable $event to allow custom error handling scenarios.
            $event = new \Auth0\Laravel\Event\Stateful\AuthenticationFailed($exception, true);
            event($event);

            // If the event was not hooked by the host application, throw an exception:
            if ($event->getThrowException()) {
                throw $exception;
            }
        }

        if (null !== $request->query('error') && null !== $request->query('error_description')) {
            // Workaround to aid static analysis, due to the mixed formatting of the query() response:
            $error = $request->query('error', '');
            $errorDescription = $request->query('error_description', '');
            $error = \is_string($error) ? $error : '';
            $errorDescription = \is_string($errorDescription) ? $errorDescription : '';

            $credentials = [
                'code' => $code,
                'state' => $state,
                'error' => ['error' => $error, 'description' => $errorDescription]
            ];

            event(new \Illuminate\Auth\Events\Failed($guard::class, $guard->user(), $credentials));

            // Clear the local session via the Auth0-PHP SDK:
            app(\Auth0\Laravel\Auth0::class)->getSdk()->clear();

            // Create a dynamic exception to report the API error response:
            $exception = \Auth0\Laravel\Exception\Stateful\CallbackException::apiException($error, $errorDescription);

            // Throw hookable $event to allow custom error handling scenarios:
            $event = new \Auth0\Laravel\Event\Stateful\AuthenticationFailed($exception, true);
            event($event);

            // If the event was not hooked by the host application, throw an exception:
            if ($event->getThrowException()) {
                throw $exception;
            }
        }

        // Ensure we have a valid user:
        $user = $guard->user();

        if (null !== $user) {
            event(new \Illuminate\Auth\Events\Validated($guard::class, $user));

            $request->session()->regenerate();

            // Throw hookable event to allow custom application logic for successful logins:
            $event = new \Auth0\Laravel\Event\Stateful\AuthenticationSucceeded($user);
            event($event);
            $user = $event->getUser();

            // Apply any mutations to the user object:
            $guard->setUser($user);

            event(new \Illuminate\Auth\Events\Login($guard::class, $user, true));
            event(new \Illuminate\Auth\Events\Authenticated($guard::class, $user));
        }

        return redirect()->intended(config('auth0.routes.home', '/')); // @phpstan-ignore-line
    }
}
