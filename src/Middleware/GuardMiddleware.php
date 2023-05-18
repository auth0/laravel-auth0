<?php

declare(strict_types=1);

namespace Auth0\Laravel\Middleware;

use Illuminate\Http\Request;

/**
 * Assigns a specific guard to the request.
 *
 * @api
 */
final class GuardMiddleware extends GuardMiddlewareAbstract implements GuardMiddlewareContract
{
}
