<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract;

interface StateInstance
{
    /**
     * Set the authenticated user context for the current request.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user An authenticated user context.
     */
    public function setUser(
        ?\Illuminate\Contracts\Auth\Authenticatable $user
    ): self;

    /**
     * Return the authenticated user context for the current request.
     */
    public function getUser(): ?\Illuminate\Contracts\Auth\Authenticatable;
}
