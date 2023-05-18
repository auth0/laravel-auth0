<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @internal
 *
 * @api
 */
abstract class TokenVerificationSucceededAbstract extends EventAbstract
{
    public function __construct(
        protected string $token,
        protected array $payload,
    ) {
    }

    final public function getPayload(): array
    {
        return $this->payload;
    }

    final public function getToken(): string
    {
        return $this->token;
    }
}
