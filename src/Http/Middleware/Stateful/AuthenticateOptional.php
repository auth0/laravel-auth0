<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware\Stateful;

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Contract\Auth\GuardContract;
use Auth0\Laravel\Contract\Entities\CredentialContract;
use Auth0\Laravel\Contract\Http\Middleware\Stateful\AuthenticateOptional as AuthenticateOptionalContract;
use Auth0\Laravel\Event\Middleware\StatefulRequest;
use Auth0\Laravel\Http\Middleware\MiddlewareAbstract;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * This middleware will configure the authenticated user for the session using a
 * previously established Auth0-PHP SDK session. If a session is not available,
 * the authenticated user will be set as null.
 *
 * @deprecated 7.8.0 This middleware is no longer necessary when using Auth0\Laravel\Auth\Guards\SessionGuard.
 */
final class AuthenticateOptional extends MiddlewareAbstract implements AuthenticateOptionalContract
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
        event(new StatefulRequest($request, $guard));

        $credential = $guard->find(Guard::SOURCE_SESSION);

        if ($credential instanceof CredentialContract && ('' === $scope || $guard->hasScope($scope, $credential))) {
            $guard->login($credential);
        }

        return $next($request);
    }
}
