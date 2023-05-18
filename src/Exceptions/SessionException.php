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
final class SessionException extends SessionExceptionAbstract implements SessionExceptionContract
{
}
