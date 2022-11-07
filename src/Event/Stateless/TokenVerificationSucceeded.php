<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateless;

final class TokenVerificationSucceeded extends \Auth0\Laravel\Event\Auth0Event implements \Auth0\Laravel\Contract\Event\Stateless\TokenVerificationSucceeded
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        private string $token,
        private array $payload
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}
