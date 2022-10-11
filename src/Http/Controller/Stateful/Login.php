<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller\Stateful;

final class Login implements \Auth0\Laravel\Contract\Http\Controller\Stateful\Login
{
    /**
     * {@inheritdoc}
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function __invoke(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        if (auth()->guard('auth0')->check())
        {
            return redirect()->intended(app()->make('config')->get('auth0.routes.home', '/'));
        }

        return redirect()->away(app(\Auth0\Laravel\Auth0::class)->getSdk()->login());
    }
}
