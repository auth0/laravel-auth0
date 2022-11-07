<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Illuminate\Contracts\Auth\Authenticatable;

final class AuthenticationSucceeded extends \Auth0\Laravel\Event\Auth0Event implements \Auth0\Laravel\Contract\Event\Stateful\AuthenticationSucceeded
{
    /**
     * {@inheritdoc}
     */
    public function __construct(private Authenticatable $user)
    {
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
