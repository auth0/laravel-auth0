<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events\Middleware;

use Auth0\Laravel\Guards\GuardContract;
use Auth0\Laravel\Events\EventAbstract;
use Illuminate\Http\Request;

final class StatefulMiddlewareRequest extends EventAbstract implements StatefulMiddlewareRequestContract
{
    public function __construct(
        public Request $request,
        public GuardContract $guard,
    ) {
    }
}
