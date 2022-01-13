<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Illuminate\Contracts\Auth\Authenticatable;

final class AuthenticationSucceeded extends \Auth0\Laravel\Event\Auth0Event
{
    /**
     * An instance of \Illuminate\Contracts\Auth\Authenticatable representing the authenticated user.
     */
    private \Illuminate\Contracts\Auth\Authenticatable $user;

    /**
     * AuthenticationSucceeded constructor.
     *
     * @param Authenticatable $user  An instance of \Illuminate\Contracts\Auth\Authenticatable representing the authenticated user.
     */
    public function __construct(
        \Illuminate\Contracts\Auth\Authenticatable $user
    ) {
        $this->user = $user;
    }

    /**
     * Overwrite the authenticated user.
     *
     * @param Authenticatable $user An instance of \Illuminate\Contracts\Auth\Authenticatable representing the authenticated user.
     */
    public function setUser(
        \Illuminate\Contracts\Auth\Authenticatable $user
    ): self {
        $this->user = $user;
        $this->mutated = true;
        return $this;
    }

    /**
     * Return the authenticated user.
     */
    public function getUser(): \Illuminate\Contracts\Auth\Authenticatable
    {
        return $this->user;
    }
}
