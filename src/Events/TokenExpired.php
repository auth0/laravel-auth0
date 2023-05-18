<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Raised when a token has expired.
 *
 * @api
 */
final class TokenExpired extends TokenExpiredAbstract implements TokenExpiredContract
{
}
