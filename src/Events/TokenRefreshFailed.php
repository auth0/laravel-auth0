<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Raised when a token refresh attempt fails.
 *
 * @api
 */
final class TokenRefreshFailed extends TokenRefreshFailedAbstract implements TokenRefreshFailedContract
{
}
