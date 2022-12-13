<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware\Stateful;

use Auth0\Laravel\Contract\Auth\Guard;

/**
 * This middleware will configure the authenticated user for the session using a
 * previously established Auth0-PHP SDK session. If a session is not available,
 * the authenticated user will be set as null.
 */
final class AuthenticateOptional implements \Auth0\Laravel\Contract\Http\Middleware\Stateful\AuthenticateOptional
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

        event(new \Auth0\Laravel\Event\Middleware\StatefulRequest($request, $guard));

        /**
         * @var Guard $guard
         */
        $user = $guard->user();

        if (null !== $user && $user instanceof \Auth0\Laravel\Contract\Model\Stateful\User) {
            $guard->login($user);
        }

        return $next($request);
    }
}
