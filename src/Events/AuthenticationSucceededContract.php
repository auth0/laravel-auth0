<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * @api
 */
interface AuthenticationSucceededContract extends EventContract
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
