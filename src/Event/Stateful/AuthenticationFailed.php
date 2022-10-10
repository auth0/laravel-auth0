<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

final class AuthenticationFailed extends \Auth0\Laravel\Event\Auth0Event implements \Auth0\Laravel\Contract\Event\Stateful\AuthenticationFailed
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
     * @inheritdoc
     */
    public function __construct(\Throwable $exception, bool $throwException = true)
    {
        $this->exception = $exception;
        $this->throwException = $throwException;
    }

    /**
     * @inheritdoc
     */
    public function setException(\Throwable $exception): self
    {
        $this->exception = $exception;
        $this->mutated = true;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getException(): \Throwable
    {
        return $this->exception;
    }

    /**
     * @inheritdoc
     */
    public function setThrowException(bool $throwException): self
    {
        $this->throwException = $throwException;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getThrowException(): bool
    {
        return $this->throwException;
    }
}
