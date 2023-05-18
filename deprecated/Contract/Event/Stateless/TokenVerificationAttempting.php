<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Event\Stateless;

use Auth0\Laravel\Events\TokenVerificationAttemptingContract;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\TokenVerificationAttempting instead.
 *
 * @api
 */
interface TokenVerificationAttempting extends TokenVerificationAttemptingContract
{
}
