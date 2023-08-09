<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Dispatched when a token is successfully verified.
 *
 * @api
 */
final class TokenVerificationSucceeded extends TokenVerificationSucceededAbstract implements TokenVerificationSucceededContract
{
}
