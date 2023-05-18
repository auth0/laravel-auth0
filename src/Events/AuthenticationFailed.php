<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Raised when an authentication attempt fails.
 *
 * @api
 */
final class AuthenticationFailed extends AuthenticationFailedAbstract implements AuthenticationFailedContract
{
}
