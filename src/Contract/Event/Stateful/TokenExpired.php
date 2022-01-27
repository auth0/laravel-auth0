<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Event\Stateful;

interface TokenExpired
{
    /**
     * AuthenticationFailed constructor.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user         An instance of \Illuminate\Contracts\Auth\Authenticatable representing the authenticated user.
     * @param bool                                       $clearSession Whether or not the local user session will be cleared after the event resolves.
     */
    public function __construct(
        \Illuminate\Contracts\Auth\Authenticatable $user,
        bool $clearSession = true
    );

    /**
     * Return the object representing the user session that has expired.
     */
    public function getUser(): \Illuminate\Contracts\Auth\Authenticatable;

    /**
     * Determine whether the provided exception will be thrown by the SDK.
     *
     * @param bool $clearException Whether or not the local user session will be cleared after the event resolves.
     */
    public function setClearSession(
        bool $clearException
    ): self;

    /**
     * Returns whether the SDK should clear the local user session after the event resolves.
     */
    public function getClearSession(): bool;
}
