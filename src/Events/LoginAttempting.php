<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Raised when a login attempt is made.
 *
 * @api
 */
final class LoginAttempting extends LoginAttemptingAbstract implements LoginAttemptingContract
{
}
