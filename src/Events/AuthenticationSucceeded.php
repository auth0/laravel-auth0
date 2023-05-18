<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Raised after a user has been successfully authenticated.
 *
 * @api
 */
final class AuthenticationSucceeded extends AuthenticationSucceededAbstract implements AuthenticationSucceededContract
{
}
