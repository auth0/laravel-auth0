<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Http\Middleware\Stateful;

use Auth0\Laravel\Middleware\AuthenticateOptionalMiddlewareContract;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Middleware\AuthenticateMiddleware instead.
 *
 * @api
 */
interface AuthenticateOptional extends AuthenticateOptionalMiddlewareContract
{
}
