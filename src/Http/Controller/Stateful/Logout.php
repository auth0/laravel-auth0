<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller\Stateful;

final class Logout implements \Auth0\Laravel\Contract\Http\Controller\Stateful\Logout
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        if (auth()->guard('auth0')->check()) {
            auth()->guard('auth0')
                ->logout();

            $request->session()
                ->invalidate();
            $request->session()
                ->regenerateToken();

            return redirect()->away(
                app(\Auth0\Laravel\Auth0::class)->getSdk()->authentication()->getLogoutLink(url(
                    app()->make('config')->get('auth0.routes.home', '/')
                ))
            );
        }

        return redirect()->intended(app()->make('config')->get('auth0.routes.home', '/'));
    }
}
