<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateless;

use Auth0\Laravel\Events\EventAbstract;
use Auth0\Laravel\Events\TokenVerificationAttemptingContract;

/**
 * Raised when a token verification attempt is made.
 *
 * @api
 */
final class TokenVerificationAttempting extends EventAbstract implements TokenVerificationAttemptingContract
{
    public function __construct(
        private string $token,
    ) {
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;
        $this->mutated = true;

        return $this;
    }
}
