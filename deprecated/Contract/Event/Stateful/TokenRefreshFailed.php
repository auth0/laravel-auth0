<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Event\Stateful;

use Auth0\Laravel\Events\TokenRefreshFailedContract;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\TokenRefreshFailed instead.
 *
 * @api
 */
interface TokenRefreshFailed extends TokenRefreshFailedContract
{
}
