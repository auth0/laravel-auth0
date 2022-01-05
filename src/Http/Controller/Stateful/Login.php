<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller\Stateful;

final class Login
{
    /**
     * Redirect to the configured Auth0 Universal Login Page if a session is not available.
     * Otherwise, redirect to the "/" route.
     *
     * @param \Illuminate\Http\Request $request The incoming request instance.
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function __invoke(
        \Illuminate\Http\Request $request
    ): \Illuminate\Http\RedirectResponse {
        if (auth()->guard('auth0')->check()) {
            return redirect()->intended('/');
        }

        return redirect()->away(app('auth0')->getSdk()->login());
    }
}
