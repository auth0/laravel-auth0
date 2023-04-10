<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware\Stateful;

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Contract\Auth\Guard as GuardContract;
use Auth0\Laravel\Contract\Http\Middleware\Stateful\Authenticate as AuthenticateContract;
use Auth0\Laravel\Event\Middleware\StatefulRequest;
use Auth0\Laravel\Http\Middleware\MiddlewareAbstract;
use Closure;
use Symfony\Component\HttpFoundation\{Response, Request};

/**
 * This middleware will configure the authenticated user for the session using a
 * previously established Auth0-PHP SDK session. If a session is not available,
 * a redirect will be issued to a route named 'login'.
 */
final class Authenticate extends MiddlewareAbstract implements AuthenticateContract
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

        if (null !== $credential) {
            if ('' === $scope || $guard->hasScope($scope, $credential)) {
                $guard->login($credential, Guard::SOURCE_SESSION);

                return $next($request);
            }

            abort(Response::HTTP_FORBIDDEN, 'Forbidden');
        }

        return redirect()->to(config('auth0.routes.login', 'login')); // @phpstan-ignore-line
    }
}
