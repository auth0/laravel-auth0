<?php

declare(strict_types=1);

namespace Auth0\Laravel\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use function is_string;

/**
 * Assigns a specific guard to the request.
 * @api
 */
final class GuardMiddleware extends GuardMiddlewareAbstract implements GuardMiddlewareContract
{
}
