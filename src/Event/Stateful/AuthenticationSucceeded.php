<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Illuminate\Contracts\Auth\Authenticatable;

final class AuthenticationSucceeded extends \Auth0\Laravel\Event\Auth0Event implements \Auth0\Laravel\Contract\Event\Stateful\AuthenticationSucceeded
{
    /**
     * An instance of \Illuminate\Contracts\Auth\Authenticatable representing the authenticated user.
     */
    private Authenticatable $user;

    /**
     * {@inheritdoc}
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function setUser(Authenticatable $user): self
    {
        $this->user = $user;
        $this->mutated = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser(): Authenticatable
    {
        return $this->user;
    }
}
