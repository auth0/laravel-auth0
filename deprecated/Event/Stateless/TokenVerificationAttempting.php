<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateless;

use Auth0\Laravel\Events\{TokenVerificationAttemptingAbstract, TokenVerificationAttemptingContract};

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\TokenVerificationAttempting instead
 *
 * @api
 */
final class TokenVerificationAttempting extends TokenVerificationAttemptingAbstract implements TokenVerificationAttemptingContract
{
}
