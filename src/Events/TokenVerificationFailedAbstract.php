<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

use Throwable;

/**
 * @internal
 *
 * @api
 */
abstract class TokenVerificationFailedAbstract extends EventAbstract
{
    public function __construct(
        protected string $token,
        protected Throwable $exception,
    ) {
    }

    final public function getException(): Throwable
    {
        return $this->exception;
    }

    final public function getToken(): string
    {
        return $this->token;
    }
}
