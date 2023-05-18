<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events\Middleware;

use Auth0\Laravel\Events\EventAbstract;
use Auth0\Laravel\Guards\GuardContract;
use Illuminate\Http\Request;

abstract class StatelessMiddlewareRequestAbstract extends EventAbstract
{
    public function __construct(
        public Request $request,
        public GuardContract $guard,
    ) {
    }
}
