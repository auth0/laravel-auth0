<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller\Stateful;

final class Logout
{
    /**
     * Redirect to Auth0's logout endpoint if a session is available.
     * Otherwise, redirect to the "/" route.
     *
     * @param \Illuminate\Http\Request $request The incoming request instance.
     */
    public function __invoke(
        \Illuminate\Http\Request $request
    ): \Illuminate\Http\RedirectResponse {
        if (auth()->guard('auth0')->check()) {
            auth()->guard('auth0')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->away(app('auth0')->getSdk()->authentication()->getLogoutLink());
        }

        return redirect()->intended('/');
    }
}
