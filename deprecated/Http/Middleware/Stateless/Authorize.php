<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware\Stateless;

use Auth0\Laravel\Guards\GuardContract;
use Auth0\Laravel\Entities\CredentialEntityContract;
use Auth0\Laravel\Event\Middleware\StatelessRequest;
use Auth0\Laravel\Http\Middleware\MiddlewareAbstract;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authorize a request using a provided bearer token. Raises an exception if a token is not available. Only for use with the deprecated Auth0\Laravel\Auth\Guard.
 *
 * @deprecated 7.8.0 This middleware is no longer necessary when using Auth0\Laravel\Guards\AuthorizationGuard. Use Laravel's native `auth` middleware instead.
 * @api
 */
final class Authorize extends MiddlewareAbstract implements AuthorizeContract
{
    public function handle(
        Request $request,
        Closure $next,
        string $scope = '',
    ): Response {
        $guard = auth()->guard();

        if (! $guard instanceof GuardContract) {
            return $next($request);
        }

        /** @var GuardContract $guard */
        event(new StatelessRequest($request, $guard));

        $credential = $guard->find(GuardContract::SOURCE_TOKEN);

        if ($credential instanceof CredentialEntityContract) {
            if ('' === $scope || $guard->hasScope($scope, $credential)) {
                $guard->login($credential);

                return $next($request);
            }

            abort(Response::HTTP_FORBIDDEN, 'Forbidden');
        }

        abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
    }
}
