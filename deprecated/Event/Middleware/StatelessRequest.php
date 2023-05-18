<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Middleware;

use Auth0\Laravel\Guards\GuardContract;
use Auth0\Laravel\Events\EventAbstract;
use Auth0\Laravel\Events\Middleware\StatelessMiddlewareRequestContract;
use Illuminate\Http\Request;

final class StatelessRequest extends EventAbstract implements StatelessMiddlewareRequestContract
{
    public function __construct(
        public Request $request,
        public GuardContract $guard,
    ) {
    }
}
