<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Middleware;

use Auth0\Laravel\Events\Middleware\{StatefulMiddlewareRequestAbstract, StatefulMiddlewareRequestContract};

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\Middleware\StatefulMiddlewareRequest instead
 *
 * @api
 */
final class StatefulRequest extends StatefulMiddlewareRequestAbstract implements StatefulMiddlewareRequestContract
{
}
