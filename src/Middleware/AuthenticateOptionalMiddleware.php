<?php

declare(strict_types=1);

namespace Auth0\Laravel\Middleware;

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Entities\CredentialEntityContract;
use Auth0\Laravel\Events\Middleware\StatefulMiddlewareRequest;
use Auth0\Laravel\Guards\GuardContract;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Try to setup an authenticated user's session state. Only for use with the deprecated Auth0\Laravel\Auth\Guard.
 *
 * @codeCoverageIgnore
 *
 * @deprecated 7.8.0 This middleware is no longer necessary when using Auth0\Laravel\Guards\AuthenticationGuard.
 *
 * @api
 */
final class AuthenticateOptionalMiddleware extends MiddlewareAbstract implements AuthenticateOptionalMiddlewareContract
{
    public function handle(
        Request $request,
        Closure $next,
        string $scope = '',
    ): Response {
        $guard = auth()->guard();

        if (! $guard instanceof GuardContract) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error');
        }

        /** @var Guard $guard */
        event(new StatefulMiddlewareRequest($request, $guard));

        $credential = $guard->find(GuardContract::SOURCE_SESSION);

        if ($credential instanceof CredentialEntityContract && ('' === $scope || $guard->hasScope($scope, $credential))) {
            $guard->login($credential);
        }

        return $next($request);
    }
}
