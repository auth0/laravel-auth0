<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Dispatched when a token fails verification.
 *
 * @api
 */
final class TokenVerificationFailed extends TokenVerificationFailedAbstract implements TokenVerificationFailedContract
{
}
