<?php

declare(strict_types=1);

namespace Auth0\Laravel\Middleware;

/**
 * @deprecated 7.8.0 This middleware is no longer necessary when using Auth0\Laravel\Guards\AuthenticationGuard.
 *
 * @api
 */
final class AuthenticateOptionalMiddleware extends AuthenticateOptionalMiddlewareAbstract implements AuthenticateOptionalMiddlewareContract
{
}
