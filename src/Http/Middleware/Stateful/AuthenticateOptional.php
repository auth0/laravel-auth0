<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware\Stateful;

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
        $user = auth()->
            guard('auth0')->
            user();

        if (null !== $user && $user instanceof \Auth0\Laravel\Contract\Model\Stateful\User) {
            auth()->guard('auth0')->
                login($user);
        }

        return $next($request);
    }
}
