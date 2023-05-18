<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

use Throwable;

/**
 * Raised when a token has failed verification.
 *
 * @api
 */
final class TokenVerificationFailed extends TokenVerificationFailedAbstract implements TokenVerificationFailedContract
{
}
