<?php

declare(strict_types=1);

namespace Auth0\Laravel\Middleware;

/**
 * @deprecated 7.8.0 This middleware is no longer necessary when using Auth0\Laravel\Guards\AuthorizationGuard.
 *
 * @api
 */
final class AuthorizeOptionalMiddleware extends AuthorizeOptionalMiddlewareAbstract implements AuthorizeOptionalMiddlewareContract
{
}
