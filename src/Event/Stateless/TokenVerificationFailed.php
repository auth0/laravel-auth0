<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateless;

use Auth0\Laravel\Contract\Event\Stateless\TokenVerificationFailed as TokenVerificationFailedContract;
use Auth0\Laravel\Event\Auth0Event;
use Throwable;

final class TokenVerificationFailed extends Auth0Event implements TokenVerificationFailedContract
{
    public function __construct(
        private string $token,
        private Throwable $exception,
    ) {
    }

    public function getException(): Throwable
    {
        return $this->exception;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
