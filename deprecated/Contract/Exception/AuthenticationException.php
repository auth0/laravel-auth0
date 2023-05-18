<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Exception;

use Auth0\Laravel\Exceptions\AuthenticationExceptionContract;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Exceptions\AuthenticationException instead.
 *
 * @api
 */
interface AuthenticationException extends AuthenticationExceptionContract
{
}
