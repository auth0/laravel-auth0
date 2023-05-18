<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exceptions;

/**
 * Exception thrown when an error occurs in the SDK's authentication flow.
 *
 * @codeCoverageIgnore
 *
 * @api
 */
interface AuthenticationExceptionContract extends ExceptionContract
{
    /**
     * @var string
     */
    public const UNAUTHENTICATED = 'Unauthenticated.';
}
