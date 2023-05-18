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
final class AuthenticationException extends AuthenticationExceptionAbstract implements AuthenticationExceptionContract
{
}
