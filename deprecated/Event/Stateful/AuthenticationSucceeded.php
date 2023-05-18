<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Auth0\Laravel\Events\AuthenticationSucceededContract;
use Auth0\Laravel\Events\EventAbstract;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Raised after a user has been successfully authenticated.
 *
 * @api
 */
final class AuthenticationSucceeded extends EventAbstract implements AuthenticationSucceededContract
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
