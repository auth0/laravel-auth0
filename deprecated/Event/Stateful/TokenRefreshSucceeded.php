<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Auth0\Laravel\Events\{TokenRefreshSucceededAbstract, TokenRefreshSucceededContract};

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\TokenRefreshSucceeded instead
 *
 * @api
 */
final class TokenRefreshSucceeded extends TokenRefreshSucceededAbstract implements TokenRefreshSucceededContract
{
}
