<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

use Auth0\Laravel\Events\EventAbstract;
use Throwable;

/**
 * Raised when an authentication attempt fails.
 *
 * @api
 */
final class AuthenticationFailed extends EventAbstract implements AuthenticationFailedContract
{
    public function __construct(
        private Throwable $exception,
        private bool $throwException = true,
    ) {
    }

    public function getException(): Throwable
    {
        return $this->exception;
    }

    public function getThrowException(): bool
    {
        return $this->throwException;
    }

    public function setException(Throwable $exception): self
    {
        $this->exception = $exception;
        $this->mutated = true;

        return $this;
    }

    public function setThrowException(bool $throwException): self
    {
        $this->throwException = $throwException;

        return $this;
    }
}
