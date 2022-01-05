<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller\Stateful;

final class Callback
{
    public function __invoke(
        \Illuminate\Http\Request $request
    ): \Illuminate\Http\RedirectResponse {
        // Check if the user already has a session:
        if (auth()->guard('auth0')->check()) {
            // They do; redirect to homepage.
            return redirect()->intended('/');
        }

        try {
            if ($request->query('state') !== null && $request->query('code') !== null) {
                app('auth0')->getSdk()->exchange();
                // var_dump(app('auth0')->getSdk()->getUser());
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
            // Workaround to aid static analysis, due to the lax formatting of the query() response:
            $error = $request->query('error') ?? '';
            $errorDescription = $request->query('error_description') ?? '';
            $error = is_string($error) ? $error : '';
            $errorDescription = is_string($errorDescription) ? $error : '';

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

        return redirect()->intended('/');
    }
}
