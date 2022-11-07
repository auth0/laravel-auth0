<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateless;

use Throwable;

final class TokenVerificationFailed extends \Auth0\Laravel\Event\Auth0Event implements \Auth0\Laravel\Contract\Event\Stateless\TokenVerificationFailed
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        private string $token,
        private Throwable $exception
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
    public function getException(): Throwable
    {
        return $this->exception;
    }
}
