<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateless;

use Auth0\Laravel\Contract\Event\Stateless\TokenVerificationAttempting as TokenVerificationAttemptingContract;
use Auth0\Laravel\Event\Auth0Event;

final class TokenVerificationAttempting extends Auth0Event implements TokenVerificationAttemptingContract
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
        $this->token   = $token;
        $this->mutated = true;

        return $this;
    }
}
