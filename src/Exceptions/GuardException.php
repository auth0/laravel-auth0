<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exceptions;

/**
 * Exception thrown when an error occurs in the SDK's guards.
 *
 * @codeCoverageIgnore
 *
 * @api
 */
final class GuardException extends GuardExceptionAbstract implements GuardExceptionContract
{
}
