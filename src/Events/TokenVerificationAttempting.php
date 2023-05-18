<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Raised when a token verification attempt is made.
 *
 * @api
 */
final class TokenVerificationAttempting extends TokenVerificationAttemptingAbstract implements TokenVerificationAttemptingContract
{
}
