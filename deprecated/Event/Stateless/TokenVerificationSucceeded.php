<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateless;

use Auth0\Laravel\Events\{TokenVerificationSucceededAbstract, TokenVerificationSucceededContract};

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\TokenVerificationSucceeded instead
 *
 * @api
 */
final class TokenVerificationSucceeded extends TokenVerificationSucceededAbstract implements TokenVerificationSucceededContract
{
}
