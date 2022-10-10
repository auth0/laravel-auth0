<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware\Stateless;

/**
 * This middleware will configure the authenticated user using an available access token.
 * If a token is not available, it will raise an exception.
 */
final class Authorize implements \Auth0\Laravel\Contract\Http\Middleware\Stateless\Authorize
{
    /**
     * {@inheritdoc}
     */
    public function handle(\Illuminate\Http\Request $request, \Closure $next, string $scope = '')
    {
        $user = auth()
            ->guard('auth0')
            ->user();

        if (null !== $user && $user instanceof \Auth0\Laravel\Contract\Model\Stateless\User) {
            if ('' !== $scope && false === auth()->guard('auth0')->hasScope($scope)) {
                return abort(403, 'Unauthorized');
            }

            auth()
                ->login($user);

            return $next($request);
        }

        return abort(403, 'Unauthorized');
    }
}
