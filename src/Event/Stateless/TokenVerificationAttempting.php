<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateless;

final class TokenVerificationAttempting extends \Auth0\Laravel\Event\Auth0Event implements \Auth0\Laravel\Contract\Event\Stateless\TokenVerificationAttempting
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        private string $token
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function setToken(string $token): self
    {
        $this->token = $token;
        $this->mutated = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken(): string
    {
        return $this->token;
    }
}
