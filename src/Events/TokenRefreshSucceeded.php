<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Dispatched when an access token is successfully refreshed.
 *
 * @api
 */
final class TokenRefreshSucceeded extends TokenRefreshSucceededAbstract implements TokenRefreshSucceededContract
{
}
