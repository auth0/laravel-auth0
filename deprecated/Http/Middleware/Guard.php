<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware;

use Auth0\Laravel\Middleware\{GuardMiddlewareAbstract, GuardMiddlewareContract};

/**
 * @deprecated 7.8.0 This middleware is no longer required. Please migrate to using either Auth0\Laravel\Guards\AuthenticationGuard or Auth0\Laravel\Guards\AuthorizationGuard.
 *
 * @api
 */
final class Guard extends GuardMiddlewareAbstract implements GuardMiddlewareContract
{
}
