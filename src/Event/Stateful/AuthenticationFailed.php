<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Auth0\Laravel\Contract\Event\Stateful\AuthenticationFailed as AuthenticationFailedContract;
use Auth0\Laravel\Event\Auth0Event;
use Throwable;

final class AuthenticationFailed extends Auth0Event implements AuthenticationFailedContract
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
        $this->mutated   = true;

        return $this;
    }

    public function setThrowException(bool $throwException): self
    {
        $this->throwException = $throwException;

        return $this;
    }
}
