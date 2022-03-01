<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware\Stateless;

/**
 * This middleware will configure the authenticated user using an available access token.
 * If a token is not available, it will raise an exception.
 *
 * @package Auth0\Laravel\Http\Middleware
 */
final class Authorize implements \Auth0\Laravel\Contract\Http\Middleware\Stateless\Authorize
{
    /**
     * @inheritdoc
     */
    public function handle(
        \Illuminate\Http\Request $request,
        \Closure $next,
        string $scope = ''
    ) {
        $user = auth()->guard('auth0')->user();

        if ($user !== null && $user instanceof \Auth0\Laravel\Contract\Model\Stateless\User) {
            if (strlen($scope) >= 1 && auth()->guard('auth0')->hasScope($scope) === false) {
                return abort(403, 'Unauthorized');
            }

            auth()->login($user);
            return $next($request);
        }

        return abort(403, 'Unauthorized');
    }
}
