<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

final class AuthenticationSucceeded
{
    private \Illuminate\Contracts\Auth\Authenticatable $user;

    public function __construct(
        \Illuminate\Contracts\Auth\Authenticatable $user
    ) {
        $this->user = $user;
    }

    public function setUser(
        \Illuminate\Contracts\Auth\Authenticatable $user
    ): self {
        $this->user = $user;
        return $this;
    }

    public function getUser(): \Illuminate\Contracts\Auth\Authenticatable
    {
        return $this->user;
    }
}
