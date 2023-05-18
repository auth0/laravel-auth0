<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exception;

use Auth0\Laravel\Exceptions\{AuthenticationExceptionAbstract, AuthenticationExceptionContract};

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Exceptions\AuthenticationException instead.
 *
 * @api
 */
final class AuthenticationException extends AuthenticationExceptionAbstract implements AuthenticationExceptionContract
{
}
