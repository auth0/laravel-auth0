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
 * Try to authorize a request using a provided bearer token. Does not raise an exception. Only for use with the deprecated Auth0\Laravel\Auth\Guard.
 *
 * @deprecated 7.8.0 This middleware is no longer necessary when using Auth0\Laravel\Guards\AuthorizationGuard.
 * @api
 */
final class AuthorizeOptional extends MiddlewareAbstract implements AuthorizeOptionalContract
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

        if ($credential instanceof CredentialEntityContract && ('' === $scope || $guard->hasScope($scope, $credential))) {
            $guard->login($credential);

            return $next($request);
        }

        return $next($request);
    }
}
