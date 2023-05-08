<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Auth0\Laravel\Contract\Event\Stateful\AuthenticationSucceeded as AuthenticationSucceededContract;
use Auth0\Laravel\Event\Auth0Event;
use Illuminate\Contracts\Auth\Authenticatable;

final class AuthenticationSucceeded extends Auth0Event implements AuthenticationSucceededContract
{
    public function __construct(private Authenticatable $user)
    {
    }

    public function getUser(): Authenticatable
    {
        return $this->user;
    }

    public function setUser(Authenticatable $user): self
    {
        $this->user = $user;
        $this->mutated = true;

        return $this;
    }
}
