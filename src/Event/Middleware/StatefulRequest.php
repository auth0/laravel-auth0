<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Middleware;

final class StatefulRequest extends \Auth0\Laravel\Event\Auth0Event implements \Auth0\Laravel\Contract\Event\Middleware\StatefulRequest
{
    public function __construct(public \Illuminate\Http\Request $request, public \Illuminate\Contracts\Auth\Guard $guard)
    {
    }
}
