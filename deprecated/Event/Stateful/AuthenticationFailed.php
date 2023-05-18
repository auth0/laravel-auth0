<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Auth0\Laravel\Events\{AuthenticationFailedAbstract, AuthenticationFailedContract};

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\AuthenticationFailed instead
 *
 * @api
 */
final class AuthenticationFailed extends AuthenticationFailedAbstract implements AuthenticationFailedContract
{
}
