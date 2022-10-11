<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware\Stateless;

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
        $user = auth()->
            guard('auth0')->
            user();

        if (null !== $user && $user instanceof \Auth0\Laravel\Contract\Model\Stateless\User)
        {
            auth()->guard('auth0')->
                login($user);
        }

        return $next($request);
    }
}
