<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Raised when a token has been successfully verified.
 *
 * @api
 */
final class TokenVerificationSucceeded extends EventAbstract implements TokenVerificationSucceededContract
{
    public function __construct(
        private string $token,
        private array $payload,
    ) {
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
