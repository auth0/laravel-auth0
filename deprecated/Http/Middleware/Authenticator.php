<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware for assigning the Auth0 session authenticator to the request.
 *
 * @internal
 * @api
 */
final class Authenticator extends MiddlewareAbstract implements AuthenticatorContract
{
    public function handle(
        Request $request,
        Closure $next,
    ): Response {
        auth()->shouldUse('auth0-session');

        return $next($request);
    }
}
