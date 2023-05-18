<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Event\Middleware;

use Auth0\Laravel\Events\Middleware\StatelessMiddlewareRequestContract;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\Middleware\StatelessMiddlewareRequest instead.
 *
 * @api
 */
interface StatelessRequest extends StatelessMiddlewareRequestContract
{
}
