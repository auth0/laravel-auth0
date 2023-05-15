<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

use Throwable;

/**
 * @api
 */
interface AuthenticationFailedContract
{
    /**
     * AuthenticationFailed constructor.
     *
     * @param Throwable $exception      an exception instance in which to throw for the authentication failure
     * @param bool      $throwException whether or not $exception will be thrown
     */
    public function __construct(Throwable $exception, bool $throwException = true);

    /**
     * Returns the exception to be thrown.
     */
    public function getException(): Throwable;

    /**
     * Returns whether the provided exception will be thrown by the SDK.
     */
    public function getThrowException(): bool;

    /**
     * Overwrite the exception to be thrown.
     *
     * @param Throwable $exception an exception instance in which to throw for the authentication failure
     */
    public function setException(Throwable $exception): self;

    /**
     * Determine whether the provided exception will be thrown by the SDK.
     *
     * @param bool $throwException whether or not $exception will be thrown
     */
    public function setThrowException(bool $throwException): self;
}
