<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

use Throwable;

/**
 * @internal
 *
 * @api
 */
abstract class AuthenticationFailedAbstract extends EventAbstract
{
    public function __construct(
        protected Throwable $exception,
        protected bool $throwException = true,
    ) {
    }

    final public function getException(): Throwable
    {
        return $this->exception;
    }

    final public function getThrowException(): bool
    {
        return $this->throwException;
    }

    final public function setException(Throwable $exception): void
    {
        $this->exception = $exception;
        $this->mutated = true;
    }

    final public function setThrowException(bool $throwException): void
    {
        $this->throwException = $throwException;
    }
}
