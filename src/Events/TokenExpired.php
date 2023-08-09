<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Dispatched when an access token has expired.
 *
 * @api
 */
final class TokenExpired extends TokenExpiredAbstract implements TokenExpiredContract
{
}
