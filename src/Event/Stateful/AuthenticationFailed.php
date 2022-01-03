<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

final class AuthenticationFailed
{
    private \Throwable $exception;

    private bool $throwException = true;

    public function __construct(
        \Throwable $exception,
        bool $throwException = true
    ) {
        $this->exception = $exception;
        $this->throwException = $throwException;
    }

    public function setException(
        \Throwable $exception
    ): self {
        $this->exception = $exception;
        return $this;
    }

    public function getException(): \Throwable
    {
        return $this->exception;
    }

    public function setThrowException(
        bool $throwException
    ): self {
        $this->throwException = $throwException;
        return $this;
    }

    public function getThrowException(): bool
    {
        return $this->throwException;
    }
}
