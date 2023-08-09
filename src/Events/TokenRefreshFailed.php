<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Dispatched when a token refresh fails.
 *
 * @api
 */
final class TokenRefreshFailed extends TokenRefreshFailedAbstract implements TokenRefreshFailedContract
{
}
