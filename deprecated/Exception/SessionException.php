<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exception;

use Auth0\Laravel\Exceptions\{SessionExceptionAbstract, SessionExceptionContract};

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Exceptions\SessionException instead.
 *
 * @api
 */
final class SessionException extends SessionExceptionAbstract implements SessionExceptionContract
{
}
