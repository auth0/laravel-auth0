<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Middleware;

use Auth0\Laravel\Contract\Event\Middleware\StatefulRequest as StatefulRequestContract;
use Auth0\Laravel\Event\Auth0Event;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

final class StatefulRequest extends Auth0Event implements StatefulRequestContract
{
    public function __construct(
        public Request $request,
        public Guard $guard
    ) {
    }
}
