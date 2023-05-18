<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Middleware;

use Auth0\Laravel\Guards\GuardContract;
use Auth0\Laravel\Events\EventAbstract;
use Auth0\Laravel\Events\Middleware\StatefulMiddlewareRequestContract;
use Illuminate\Http\Request;

/**
 * Event fired when the configuration array is being built.
 *
 * @codeCoverageIgnore
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\Middleware\StatefulRequest instead.
 * @api
 */
final class StatefulRequest extends EventAbstract implements StatefulMiddlewareRequestContract
{
    public function __construct(
        public Request $request,
        public GuardContract $guard,
    ) {
    }
}
