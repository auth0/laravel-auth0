<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Throwable;

final class AuthenticationFailed extends \Auth0\Laravel\Event\Auth0Event
{
    /**
     * An exception instance in which to throw for the authentication failure.
     */
    private \Throwable $exception;

    /**
     * Whether or not $exception will be thrown.
     */
    private bool $throwException = true;

    /**
     * AuthenticationFailed constructor.
     *
     * @param Throwable $exception      An exception instance in which to throw for the authentication failure.
     * @param bool      $throwException Whether or not $exception will be thrown.
     */
    public function __construct(
        \Throwable $exception,
        bool $throwException = true
    ) {
        $this->exception = $exception;
        $this->throwException = $throwException;
    }

    /**
     * Overwrite the exception to be thrown.
     *
     * @param Throwable $exception An exception instance in which to throw for the authentication failure.
     */
    public function setException(
        \Throwable $exception
    ): self {
        $this->exception = $exception;
        $this->mutated = true;
        return $this;
    }

    /**
     * Returns the exception to be thrown.
     */
    public function getException(): \Throwable
    {
        return $this->exception;
    }

    /**
     * Determine whether the provided exception will be thrown by the SDK.
     *
     * @param bool $throwException Whether or not $exception will be thrown.
     */
    public function setThrowException(
        bool $throwException
    ): self {
        $this->throwException = $throwException;
        return $this;
    }

    /**
     * Returns whether the provided exception will be thrown by the SDK.
     */
    public function getThrowException(): bool
    {
        return $this->throwException;
    }
}
