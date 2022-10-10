<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

final class AuthenticationSucceeded extends \Auth0\Laravel\Event\Auth0Event implements \Auth0\Laravel\Contract\Event\Stateful\AuthenticationSucceeded
{
    /**
     * An instance of \Illuminate\Contracts\Auth\Authenticatable representing the authenticated user.
     */
    private \Illuminate\Contracts\Auth\Authenticatable $user;

    /**
     * @inheritdoc
     */
    public function __construct(\Illuminate\Contracts\Auth\Authenticatable $user)
    {
        $this->user = $user;
    }

    /**
     * @inheritdoc
     */
    public function setUser(\Illuminate\Contracts\Auth\Authenticatable $user): self
    {
        $this->user = $user;
        $this->mutated = true;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUser(): \Illuminate\Contracts\Auth\Authenticatable
    {
        return $this->user;
    }
}
