<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller\Stateful;

final class Callback implements \Auth0\Laravel\Contract\Http\Controller\Stateful\Callback
{
    /**
     * @inheritdoc
     */
    public function __invoke(
        \Illuminate\Http\Request $request
    ): \Illuminate\Http\RedirectResponse {
        // Check if the user already has a session:
        if (auth()->guard('auth0')->check()) {
            // They do; redirect to homepage.
            return redirect()->intended(app()->make('config')->get('auth0.routes.home', '/'));
        }

        try {
            if ($request->query('state') !== null && $request->query('code') !== null) {
                app('auth0')->getSdk()->exchange();
            }
        } catch (\Throwable $exception) {
            app('auth0')->getSdk()->clear();

            // Throw hookable $event to allow custom error handling scenarios.
            $event = new \Auth0\Laravel\Event\Stateful\AuthenticationFailed($exception, true);
            event($event);

            // If the event was not hooked by the host application, throw an exception:
            if ($event->getThrowException() === true) {
                throw $exception;
            }
        }

        if ($request->query('error') !== null && $request->query('error_description') !== null) {
            // Workaround to aid static analysis, due to the mixed formatting of the query() response:
            $error = $request->query('error', '');
            $errorDescription = $request->query('error_description', '');
            $error = is_string($error) ? $error : '';
            $errorDescription = is_string($errorDescription) ? $errorDescription : '';

            // Clear the local session via the Auth0-PHP SDK:
            app('auth0')->getSdk()->clear();

            // Create a dynamic exception to report the API error response:
            $exception = \Auth0\Laravel\Exception\Stateful\CallbackException::apiException($error, $errorDescription);

            // Throw hookable $event to allow custom error handling scenarios:
            $event = new \Auth0\Laravel\Event\Stateful\AuthenticationFailed($exception, true);
            event($event);

            // If the event was not hooked by the host application, throw an exception:
            if ($event->getThrowException() === true) {
                throw $exception;
            }
        }

        // Ensure we have a valid user:
        $user = auth()->guard('auth0')->user();

        if ($user !== null) {
            // Throw hookable event to allow custom application logic for successful logins:
            $event = new \Auth0\Laravel\Event\Stateful\AuthenticationSucceeded($user);
            event($event);

            // Apply any mutations to the user object:
            auth()->guard('auth0')->setUser($event->getUser());
        }

        return redirect()->intended(app()->make('config')->get('auth0.routes.home', '/'));
    }
}
