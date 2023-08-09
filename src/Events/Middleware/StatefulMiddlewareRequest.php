<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events\Middleware;

/**
 * Dispatched when an incoming request is being processed by the stateful middleware.
 *
 * @api
 */
final class StatefulMiddlewareRequest extends StatefulMiddlewareRequestAbstract implements StatefulMiddlewareRequestContract
{
}
