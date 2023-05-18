<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exception;

use Auth0\Laravel\Exceptions\GuardExceptionAbstract;
use Auth0\Laravel\Exceptions\GuardExceptionContract;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Exceptions\GuardException instead.
 * @api
 */
final class GuardException extends GuardExceptionAbstract implements GuardExceptionContract
{
}
