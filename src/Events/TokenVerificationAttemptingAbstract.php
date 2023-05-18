<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @internal
 * @api
 */
abstract class TokenVerificationAttemptingAbstract extends EventAbstract
{
    public function __construct(
        private string $token,
    ) {
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
        $this->mutated = true;
    }
}
