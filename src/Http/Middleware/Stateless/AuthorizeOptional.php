<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware\Stateless;

/**
 * This middleware will configure the authenticated user using an available access token.
 *
 * @package Auth0\Laravel\Http\Middleware
 */
final class AuthorizeOptional
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
