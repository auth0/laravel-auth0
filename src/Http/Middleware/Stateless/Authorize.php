<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware\Stateless;

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Contract\Auth\Guard as GuardContract;
use Auth0\Laravel\Contract\Http\Middleware\Stateless\Authorize as AuthorizeContract;
use Auth0\Laravel\Event\Middleware\StatelessRequest;
use Auth0\Laravel\Http\Middleware\MiddlewareAbstract;
use Closure;
use Illuminate\Http\{JsonResponse, Request, Response};

/**
 * This middleware will configure the authenticated user using an available access token.
 * If a token is not available, it will raise an exception.
 */
final class Authorize extends MiddlewareAbstract implements AuthorizeContract
{
    public function handle(
        Request $request,
        Closure $next,
        string $scope = '',
    ): Response | JsonResponse {
        $guard = auth()->guard();

        if (! $guard instanceof GuardContract) {
            return $next($request);
        }

        /** @var Guard $guard */
        event(new StatelessRequest($request, $guard));

        $credential = $guard->find(Guard::SOURCE_TOKEN);

        if (null !== $credential) {
            if ('' === $scope || $guard->hasScope($scope, $credential)) {
                $guard->login($credential, Guard::SOURCE_TOKEN);

                return $next($request);
            }

            abort(Response::HTTP_FORBIDDEN, 'Forbidden');
        }

        abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
    }
}
