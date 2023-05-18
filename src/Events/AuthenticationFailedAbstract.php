<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

use Throwable;

/**
 * @internal
 * @api
 */
abstract class AuthenticationFailedAbstract extends EventAbstract
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

    public function setException(Throwable $exception): void
    {
        $this->exception = $exception;
        $this->mutated = true;
    }

    public function setThrowException(bool $throwException): void
    {
        $this->throwException = $throwException;
    }
}
