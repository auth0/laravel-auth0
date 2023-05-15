<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exceptions;

/**
 * Exception thrown when an error occurs with the Laravel session store.
 *
 * @codeCoverageIgnore
 *
 * @api
 */
interface SessionExceptionContract extends ExceptionContract
{
    /**
     * @var string
     */
    public const LARAVEL_SESSION_INACCESSIBLE = 'The Laravel session store is inaccessible.';
}
