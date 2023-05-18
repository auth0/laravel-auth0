<?php

declare(strict_types=1);

namespace Auth0\Laravel\Middleware;

use Auth0\Laravel\Guards\{AuthenticationGuardContract, AuthorizationGuardContract};
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @codeCoverageIgnore
 *
 * @internal
 *
 * @api
 */
abstract class MiddlewareAbstract
{
    final public function getAuthenticationGuard(
        ?string $guard = null,
    ): AuthenticationGuardContract {
        return app('auth0.authenticator');
    }

    final public function getAuthorizationGuard(
        ?string $guard = null,
    ): AuthorizationGuardContract {
        return app('auth0.authorizer');
    }

    abstract public function handle(
        Request $request,
        Closure $next,
    ): Response;
}
