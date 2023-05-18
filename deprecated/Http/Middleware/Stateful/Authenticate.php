<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware\Stateful;

use Auth0\Laravel\Guards\GuardContract;
use Auth0\Laravel\Entities\CredentialEntityContract;
use Auth0\Laravel\Event\Middleware\StatefulRequest;
use Auth0\Laravel\Http\Middleware\MiddlewareAbstract;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Setup an authenticated user's session state. Redirect unauthenticated requests to the `/login` route. Only for use with the deprecated Auth0\Laravel\Auth\Guard.
 *
 * @deprecated 7.8.0 This middleware is no longer necessary when using Auth0\Laravel\Guards\AuthenticationGuard. Use Laravel's native `auth` middleware instead.
 * @api
 */
final class Authenticate extends MiddlewareAbstract implements AuthenticateContract
{
    public function handle(
        Request $request,
        Closure $next,
        string $scope = '',
    ): Response {
        $guard = auth()->guard();
        $scope = trim($scope);

        if (! $guard instanceof GuardContract) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error');
        }

        /** @var GuardContract $guard */
        event(new StatefulRequest($request, $guard));

        $credential = $guard->find(GuardContract::SOURCE_SESSION);

        if ($credential instanceof CredentialEntityContract) {
            if ('' === $scope || $guard->hasScope($scope, $credential)) {
                $guard->login($credential);

                return $next($request);
            }

            abort(Response::HTTP_FORBIDDEN, 'Forbidden');
        }

        return redirect()
            ->setIntendedUrl($request->fullUrl())
            ->to(config('auth0.routes.login', 'login')); // @phpstan-ignore-line
    }
}
