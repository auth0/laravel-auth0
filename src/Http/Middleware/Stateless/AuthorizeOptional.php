<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware\Stateless;

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Contract\Auth\GuardContract;
use Auth0\Laravel\Contract\Entities\CredentialContract;
use Auth0\Laravel\Contract\Http\Middleware\Stateless\AuthorizeOptional as AuthorizeOptionalContract;
use Auth0\Laravel\Event\Middleware\StatelessRequest;
use Auth0\Laravel\Http\Middleware\MiddlewareAbstract;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * This middleware will configure the authenticated user using an available access token.
 *
 * @deprecated 7.8.0 This middleware is no longer necessary when using Auth0\Laravel\Auth\Guards\TokenGuard.
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

        /** @var Guard $guard */
        event(new StatelessRequest($request, $guard));

        $credential = $guard->find(Guard::SOURCE_TOKEN);

        if ($credential instanceof CredentialContract && ('' === $scope || $guard->hasScope($scope, $credential))) {
            $guard->login($credential);

            return $next($request);
        }

        return $next($request);
    }
}
