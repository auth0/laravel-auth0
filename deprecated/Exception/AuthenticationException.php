<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exception;

use Auth0\Laravel\Exceptions\AuthenticationExceptionContract;
use Auth0\Laravel\Exceptions\ExceptionAbstract;

/**
 * Exception thrown when an error occurs in the SDK's authentication flow.
 *
 * @codeCoverageIgnore
 * @api
 */
final class AuthenticationException extends ExceptionAbstract implements AuthenticationExceptionContract
{
}
