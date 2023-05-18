<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exception;

use Auth0\Laravel\Exceptions\ExceptionAbstract;
use Auth0\Laravel\Exceptions\SessionExceptionContract;

/**
 * Exception thrown when an error occurs with the Laravel session store.
 *
 * @codeCoverageIgnore
 * @api
 */
final class SessionException extends ExceptionAbstract implements SessionExceptionContract
{
}
