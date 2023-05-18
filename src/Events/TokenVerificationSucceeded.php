<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Raised when a token has been successfully verified.
 *
 * @api
 */
final class TokenVerificationSucceeded extends TokenVerificationSucceededAbstract implements TokenVerificationSucceededContract
{
}
