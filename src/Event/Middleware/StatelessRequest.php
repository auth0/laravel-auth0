<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Middleware;

use Auth0\Laravel\Contract\Event\Middleware\StatelessRequest as StatelessRequestContract;
use Auth0\Laravel\Event\Auth0Event;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

final class StatelessRequest extends Auth0Event implements StatelessRequestContract
{
    public function __construct(
        public Request $request,
        public Guard $guard
    ) {
    }
}
