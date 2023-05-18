<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Http\Middleware\Stateless;

use Auth0\Laravel\Middleware\AuthorizeMiddlewareContract;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Middleware\AuthorizeMiddleware instead.
 *
 * @api
 */
interface Authorize extends AuthorizeMiddlewareContract
{
}
