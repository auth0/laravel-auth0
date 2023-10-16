<?php

declare(strict_types=1);

namespace Auth0\Laravel\Middleware;

use Auth0\Laravel\Entities\CredentialEntityContract;
use Auth0\Laravel\Events;
use Auth0\Laravel\Events\Middleware\StatefulMiddlewareRequest;
use Auth0\Laravel\Guards\{AuthenticationGuardContract, GuardContract};
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated 7.8.0 This middleware is no longer necessary when using Auth0\Laravel\Guards\AuthenticationGuard. Use Laravel's standard `auth` middleware instead.
 *
 * @api
 */
abstract class AuthenticateMiddlewareAbstract extends MiddlewareAbstract
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
        $scope = trim($scope);

        if (! $guard instanceof GuardContract) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error');
        }

        Events::dispatch(new StatefulMiddlewareRequest($request, $guard));

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
            ->to('/login'); // @phpstan-ignore-line
    }
}
