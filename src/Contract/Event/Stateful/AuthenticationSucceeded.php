<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Event\Stateful;

interface AuthenticationSucceeded
{
    /**
     * AuthenticationSucceeded constructor.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user  An instance of \Illuminate\Contracts\Auth\Authenticatable representing the authenticated user.
     */
    public function __construct(
        \Illuminate\Contracts\Auth\Authenticatable $user
    );

    /**
     * Overwrite the authenticated user.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user An instance of \Illuminate\Contracts\Auth\Authenticatable representing the authenticated user.
     */
    public function setUser(
        \Illuminate\Contracts\Auth\Authenticatable $user
    ): self;

    /**
     * Return the authenticated user.
     */
    public function getUser(): \Illuminate\Contracts\Auth\Authenticatable;
}
