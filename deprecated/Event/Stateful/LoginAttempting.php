<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Auth0\Laravel\Events\LoginAttemptingAbstract;
use Auth0\Laravel\Events\LoginAttemptingContract;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\LoginAttempting instead
 *
 * @api
 */
final class LoginAttempting extends LoginAttemptingAbstract implements LoginAttemptingContract
{
}
