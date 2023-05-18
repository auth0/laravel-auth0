<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Event\Stateful;

use Auth0\Laravel\Events\TokenExpiredContract;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\TokenExpired instead.
 *
 * @api
 */
interface TokenExpired extends TokenExpiredContract
{
}
