<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Event\Stateful;

use Illuminate\Contracts\Auth\Authenticatable;

interface AuthenticationSucceeded
{
    /**
     * AuthenticationSucceeded constructor.
     *
     * @param Authenticatable $user an instance of Authenticatable representing the authenticated user
     */
    public function __construct(Authenticatable $user);

    /**
     * Return the authenticated user.
     */
    public function getUser(): Authenticatable;

    /**
     * Overwrite the authenticated user.
     *
     * @param Authenticatable $user an instance of Authenticatable representing the authenticated user
     */
    public function setUser(Authenticatable $user): self;
}
