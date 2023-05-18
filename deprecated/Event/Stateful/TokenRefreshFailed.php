<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Auth0\Laravel\Events\{TokenRefreshFailedAbstract, TokenRefreshFailedContract};

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\TokenRefreshFailed instead
 *
 * @api
 */
final class TokenRefreshFailed extends TokenRefreshFailedAbstract implements TokenRefreshFailedContract
{
}
