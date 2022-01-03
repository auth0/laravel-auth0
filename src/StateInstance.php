<?php

declare(strict_types=1);

namespace Auth0\Laravel;

final class StateInstance
{
    private ?\Illuminate\Contracts\Auth\Authenticatable $user = null;

    public function setUser(
        ?\Illuminate\Contracts\Auth\Authenticatable $user
    ): self {
        $this->user = $user;
        return $this;
    }

    public function getUser(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        return $this->user;
    }
}
