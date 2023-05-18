<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Event\Stateful;

use Auth0\Laravel\Events\TokenExpiredContract;

/**
 * @codeCoverageIgnore
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\TokenExpiredContract instead.
 * @api
 */
interface TokenExpired extends TokenExpiredContract
{
}
