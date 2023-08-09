<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Dispatched when a user successfully authenticates.
 *
 * @api
 */
final class AuthenticationSucceeded extends AuthenticationSucceededAbstract implements AuthenticationSucceededContract
{
}
