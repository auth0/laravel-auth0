<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Dispatched when user authentication fails.
 *
 * @api
 */
final class AuthenticationFailed extends AuthenticationFailedAbstract implements AuthenticationFailedContract
{
}
