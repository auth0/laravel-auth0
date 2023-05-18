<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateless;

use Auth0\Laravel\Events\TokenVerificationFailedAbstract;
use Auth0\Laravel\Events\TokenVerificationFailedContract;
use Throwable;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\TokenVerificationFailed instead
 *
 * @api
 */
final class TokenVerificationFailed extends TokenVerificationFailedAbstract implements TokenVerificationFailedContract
{
}
