<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Auth0\Laravel\Events\{AuthenticationSucceededAbstract, AuthenticationSucceededContract};

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\AuthenticationSucceeded instead
 *
 * @api
 */
final class AuthenticationSucceeded extends AuthenticationSucceededAbstract implements AuthenticationSucceededContract
{
}
