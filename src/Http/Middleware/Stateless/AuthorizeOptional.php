<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware\Stateless;

use Auth0\Laravel\Contract\Auth\Guard;

/**
 * This middleware will configure the authenticated user using an available access token.
 */
final class AuthorizeOptional implements \Auth0\Laravel\Contract\Http\Middleware\Stateless\AuthorizeOptional
{
    /**
     * {@inheritdoc}
     */
    public function handle(\Illuminate\Http\Request $request, \Closure $next)
    {
        $auth = auth();

        /**
         * @var \Illuminate\Contracts\Auth\Factory $auth
         */
        $guard = $auth->guard('auth0');

        event(new \Auth0\Laravel\Event\Middleware\StatelessRequest($request, $guard));

        /**
         * @var Guard $guard
         */
        $user = $guard->user();

        if (null !== $user && $user instanceof \Auth0\Laravel\Contract\Model\Stateless\User) {
            $guard->login($user);
        }

        return $next($request);
    }
}
