<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Dispatched when a user tries to login.
 *
 * @api
 */
final class LoginAttempting extends LoginAttemptingAbstract implements LoginAttemptingContract
{
}
