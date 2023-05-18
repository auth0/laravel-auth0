<?php

declare(strict_types=1);

namespace Auth0\Laravel\Middleware;

/**
 * @deprecated 7.8.0 This middleware is no longer necessary when using Auth0\Laravel\Guards\AuthorizationGuard. Use Laravel's standard `auth` middleware instead.
 *
 * @api
 */
final class AuthorizeMiddleware extends AuthorizeMiddlewareAbstract implements AuthorizeMiddlewareContract
{
}
