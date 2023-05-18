<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Auth0\Laravel\Events\EventAbstract;
use Auth0\Laravel\Events\TokenExpiredContract;

/**
 * Raised when a token has expired.
 *
 * @api
 */
final class TokenExpired extends EventAbstract implements TokenExpiredContract
{
}
