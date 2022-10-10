<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Exception\Stateful;

interface CallbackException
{
    /**
     * Thrown when an API exception is encountered in an underlying network request.
     *
     * @param  string  $error  The error message to return.
     * @param  string  $errorDescription  The error description to return.
     */
    public static function apiException(string $error, string $errorDescription): self;
}
