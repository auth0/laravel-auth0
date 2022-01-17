<?php

declare(strict_types=1);

namespace Auth0\Laravel;

final class StateInstance
{
    /**
     * An authenticated user context for the current request.
     */
    private ?\Illuminate\Contracts\Auth\Authenticatable $user = null;

    /**
     * @inheritdoc
     */
    public function setUser(
        ?\Illuminate\Contracts\Auth\Authenticatable $user
    ): self {
        $this->user = $user;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUser(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        return $this->user;
    }
}
