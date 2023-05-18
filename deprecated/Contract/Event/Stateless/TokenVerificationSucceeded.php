<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Event\Stateless;

use Auth0\Laravel\Events\TokenVerificationSucceededContract;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\TokenVerificationSucceeded instead.
 * @api
 */
interface TokenVerificationSucceeded extends TokenVerificationSucceededContract
{
}
