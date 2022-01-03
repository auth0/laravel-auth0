<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller\Stateful;

final class Logout
{
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
