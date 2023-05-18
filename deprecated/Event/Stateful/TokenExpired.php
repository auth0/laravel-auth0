<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Auth0\Laravel\Events\{TokenExpiredAbstract, TokenExpiredContract};

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\TokenExpired instead
 *
 * @api
 */
final class TokenExpired extends TokenExpiredAbstract implements TokenExpiredContract
{
}
