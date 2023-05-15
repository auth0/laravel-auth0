<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Raised after a token has been successfully refreshed.
 *
 * @api
 */
final class TokenRefreshSucceeded extends EventAbstract implements TokenRefreshSucceededContract
{
}
