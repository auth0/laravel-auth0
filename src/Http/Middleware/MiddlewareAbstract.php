<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware;

use Auth0\Laravel\Contract\Auth\Guards\{SessionGuardContract, TokenGuardContract};
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @codeCoverageIgnore
 */
abstract class MiddlewareAbstract
{
    final public function getAuthenticationGuard(
        ?string $guard = null,
    ): SessionGuardContract {
        return app('auth0.authenticator');
    }

    final public function getAuthorizationGuard(
        ?string $guard = null,
    ): TokenGuardContract {
        return app('auth0.authorizer');
    }

    abstract public function handle(
        Request $request,
        Closure $next,
        string $scope = '',
    ): Response;
}
