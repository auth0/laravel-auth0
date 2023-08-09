<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Dispatched before a token verification is performed.
 *
 * @api
 */
final class TokenVerificationAttempting extends TokenVerificationAttemptingAbstract implements TokenVerificationAttemptingContract
{
}
