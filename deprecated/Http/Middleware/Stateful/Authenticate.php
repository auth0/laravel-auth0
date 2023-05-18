<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware\Stateful;

use Auth0\Laravel\Middleware\AuthenticateMiddlewareAbstract;
use Auth0\Laravel\Middleware\AuthenticateMiddlewareContract;

/**
 * @deprecated 7.8.0 This middleware is no longer required. Please migrate to using Auth0\Laravel\Guards\AuthenticationGuard and Laravel's standard `auth` middleware instead.
 * @api
 */
final class AuthenticateMiddleware extends AuthenticateMiddlewareAbstract implements AuthenticateMiddlewareContract
{
}
