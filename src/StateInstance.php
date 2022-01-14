<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Illuminate\Contracts\Auth\Authenticatable;

final class StateInstance
{
    /**
     * An authenticated user context for the current request.
     */
    private ?\Illuminate\Contracts\Auth\Authenticatable $user = null;

    /**
     * Set the authenticated user context for the current request.
     *
     * @param Authenticatable|null $user An authenticated user context.
     */
    public function setUser(
        ?\Illuminate\Contracts\Auth\Authenticatable $user
    ): self {
        $this->user = $user;
        return $this;
    }

    /**
     * Return the authenticated user context for the current request.
     */
    public function getUser(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        return $this->user;
    }
}
