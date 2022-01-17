<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware\Stateful;

/**
 * This middleware will configure the authenticated user for the session using a
 * previously established Auth0-PHP SDK session. If a session is not available,
 * a redirect will be issued to a route named 'login'.
 *
 * @package Auth0\Laravel\Http\Middleware
 */
final class Authenticate implements \Auth0\Laravel\Contract\Http\Middleware\Stateful\Authenticate
{
    /**
     * @inheritdoc
     */
    public function handle(
        \Illuminate\Http\Request $request,
        \Closure $next
    ) {
        if (auth()->guard('auth0')->check()) {
            auth()->guard('auth0')->login(auth()->guard('auth0')->user());
            return $next($request);
        }

        return redirect('login');
    }
}
