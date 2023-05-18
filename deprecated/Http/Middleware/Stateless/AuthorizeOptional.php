<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware\Stateless;

use Auth0\Laravel\Middleware\AuthorizeOptionalMiddlewareAbstract;
use Auth0\Laravel\Middleware\AuthorizeOptionalMiddlewareContract;

/**
 * @deprecated 7.8.0 This middleware is no longer required. Please migrate to using Auth0\Laravel\Guards\AuthorizationGuard.
 * @api
 */
final class AuthorizeOptionalMiddleware extends AuthorizeOptionalMiddlewareAbstract implements AuthorizeOptionalMiddlewareContract
{
}
