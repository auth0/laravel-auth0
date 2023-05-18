<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event;

use Auth0\Laravel\Events\Auth0EventContract;
use Auth0\Laravel\Events\EventAbstract;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\EventAbstract instead.
 *
 * @codeCoverageIgnore
 * @api
 */
abstract class Auth0Event extends EventAbstract implements Auth0EventContract
{
}
