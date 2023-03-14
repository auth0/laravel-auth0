<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateless;

use Auth0\Laravel\Contract\Event\Stateless\TokenVerificationSucceeded as TokenVerificationSucceededContract;
use Auth0\Laravel\Event\Auth0Event;

final class TokenVerificationSucceeded extends Auth0Event implements TokenVerificationSucceededContract
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
