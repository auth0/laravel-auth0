<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Auth0\Laravel\Events\EventAbstract;
use Auth0\Laravel\Events\TokenRefreshFailedContract;

/**
 * Raised when a token refresh attempt fails.
 *
 * @api
 */
final class TokenRefreshFailed extends EventAbstract implements TokenRefreshFailedContract
{
}
