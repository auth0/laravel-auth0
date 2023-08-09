<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events\Middleware;

/**
 * Dispatched when an incoming request is being processed by the stateless middleware.
 *
 * @api
 */
final class StatelessMiddlewareRequest extends StatelessMiddlewareRequestAbstract implements StatelessMiddlewareRequestContract
{
}
