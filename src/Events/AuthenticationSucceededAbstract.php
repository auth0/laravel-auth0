<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * @internal
 *
 * @api
 */
abstract class AuthenticationSucceededAbstract extends EventAbstract
{
    public function __construct(protected Authenticatable $user)
    {
    }

    final public function getUser(): Authenticatable
    {
        return $this->user;
    }

    final public function setUser(Authenticatable $user): void
    {
        $this->user = $user;
        $this->mutated = true;
    }
}
