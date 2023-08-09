<?php

declare(strict_types=1);

namespace Auth0\Laravel\Middleware;

use Auth0\Laravel\Entities\CredentialEntityContract;
use Auth0\Laravel\Events;
use Auth0\Laravel\Events\Middleware\StatelessMiddlewareRequest;
use Auth0\Laravel\Guards\{AuthenticationGuardContract, GuardContract};
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated 7.8.0 This middleware is no longer necessary when using Auth0\Laravel\Guards\AuthorizationGuard.
 *
 * @api
 */
abstract class AuthorizeOptionalMiddlewareAbstract extends MiddlewareAbstract
{
    /**
     * @psalm-suppress UndefinedInterfaceMethod
     *
     * @param Request $request
     * @param Closure $next
     * @param string  $scope
     */
    final public function handle(
        Request $request,
        Closure $next,
        string $scope = '',
    ): Response {
        $guard = auth()->guard();

        if (! $guard instanceof GuardContract) {
            return $next($request);
        }

        Events::dispatch(new StatelessMiddlewareRequest($request, $guard));

        $credential = $guard->find(GuardContract::SOURCE_TOKEN);

        if ($credential instanceof CredentialEntityContract && ('' === $scope || $guard->hasScope($scope, $credential))) {
            $guard->login($credential);

            return $next($request);
        }

        return $next($request);
    }
}
