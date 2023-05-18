<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware\Stateful;

use Auth0\Laravel\Middleware\AuthenticateOptionalMiddlewareContract;
use Auth0\Laravel\Middleware\AuthenticateOptionalMiddlewareAbstract;

/**
 * @deprecated 7.8.0 This middleware is no longer required. Please migrate to using Auth0\Laravel\Guards\AuthenticationGuard.
 * @api
 */
final class AuthenticateOptionalMiddleware extends AuthenticateOptionalMiddlewareAbstract implements AuthenticateOptionalMiddlewareContract
{
}
