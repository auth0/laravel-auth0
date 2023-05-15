<?php

declare(strict_types=1);

namespace Auth0\Laravel\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware for assigning the Auth0 session authenticator to the request.
 *
 * @internal
 * @api
 */
final class AuthenticatorMiddleware extends MiddlewareAbstract implements AuthenticatorMiddlewareContract
{
    public function handle(
        Request $request,
        Closure $next,
    ): Response {
        auth()->shouldUse('auth0-session');

        return $next($request);
    }
}
