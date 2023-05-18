<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exception;

use Auth0\Laravel\Exceptions\ExceptionAbstract;
use Auth0\Laravel\Exceptions\GuardExceptionContract;

/**
 * Exception thrown when an error occurs in the SDK's guards.
 *
 * @codeCoverageIgnore
 * @api
 */
final class GuardException extends ExceptionAbstract implements GuardExceptionContract
{
}
