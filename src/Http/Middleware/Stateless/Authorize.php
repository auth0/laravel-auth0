<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware\Stateless;

/**
 * This middleware will configure the authenticated user using an available access token.
 * If a token is not available, it will raise an exception.
 *
 * @package Auth0\Laravel\Http\Middleware
 */
final class Authorize implements \Auth0\Laravel\Contract\Http\Middleware\Stateless\Authorize
{
    /**
     * @inheritdoc
     */
    public function handle(
        \Illuminate\Http\Request $request,
        \Closure $next
    ) {
        if (auth()->guard('auth0')->check()) {
            auth()->login(auth()->guard('auth0')->user());
            return $next($request);
        }

        return abort(403, 'Unauthorized');
    }
}
