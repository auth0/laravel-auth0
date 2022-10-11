<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller\Stateful;

use Auth0\Laravel\Contract\Auth\Guard;

final class Logout implements \Auth0\Laravel\Contract\Http\Controller\Stateful\Logout
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        $auth = auth();

        /**
         * @var \Illuminate\Contracts\Auth\Factory $auth
         */
        $guard = $auth->guard('auth0');

        /**
         * @var Guard $guard
         */
        if ($guard->check()) {
            $guard->logout();

            $request->session()->
                invalidate();
            $request->session()->
                regenerateToken();

            return redirect()->away(
                app(\Auth0\Laravel\Auth0::class)->getSdk()->authentication()->getLogoutLink(url(
                    app()->make('config')->get('auth0.routes.home', '/'),
                )),
            );
        }

        return redirect()->intended(app()->make('config')->get('auth0.routes.home', '/'));
    }
}
