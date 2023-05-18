<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @internal
 * @api
 */
abstract class TokenVerificationSucceededAbstract extends EventAbstract
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
