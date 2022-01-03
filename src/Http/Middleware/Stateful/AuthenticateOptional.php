<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware\Stateful;

/**
 * This middleware will configure the authenticated user for the session using a
 * previously established Auth0-PHP SDK session. If a session is not available,
 * the authenticated user will be set as null.
 *
 * @package Auth0\Laravel\Http\Middleware
 */
final class AuthenticateOptional
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(
        \Illuminate\Http\Request $request,
        \Closure $next
    ) {
        if (auth()->guard('auth0')->check()) {
            auth()->guard('auth0')->login(auth()->guard('auth0')->user());
        }

        return $next($request);
    }
}
