<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Middleware;

use Auth0\Laravel\Events\Middleware\StatelessMiddlewareRequestAbstract;
use Auth0\Laravel\Events\Middleware\StatelessMiddlewareRequestContract;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\Middleware\StatelessMiddlewareRequest instead
 *
 * @api
 */
final class StatelessRequest extends StatelessMiddlewareRequestAbstract implements StatelessMiddlewareRequestContract
{
}
