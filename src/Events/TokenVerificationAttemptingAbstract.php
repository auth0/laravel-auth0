<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @internal
 *
 * @api
 */
abstract class TokenVerificationAttemptingAbstract extends EventAbstract
{
    public function __construct(
        private string $token,
    ) {
    }

    final public function getToken(): string
    {
        return $this->token;
    }

    final public function setToken(string $token): void
    {
        $this->token = $token;
        $this->mutated = true;
    }
}
