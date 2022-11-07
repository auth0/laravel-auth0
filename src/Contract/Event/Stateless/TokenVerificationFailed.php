<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Event\Stateless;

use Throwable;

interface TokenVerificationFailed
{
    /**
     * AuthenticationFailed constructor.
     *
     * @param  string  $token  an encoded bearer JSON web token
     * @param  Throwable  $exception  an exception instance in which to throw for the token verification failure
     */
    public function __construct(
        string $token,
        Throwable $exception
    );

    /**
     * Return the bearer JSON web token
     */
    public function getToken(): string;

    /**
     * Returns the exception to be thrown.
     */
    public function getException(): Throwable;
}
