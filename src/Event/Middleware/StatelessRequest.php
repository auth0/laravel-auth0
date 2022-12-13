<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Middleware;

final class StatelessRequest extends \Auth0\Laravel\Event\Auth0Event implements \Auth0\Laravel\Contract\Event\Middleware\StatelessRequest
{
    public function __construct(public \Illuminate\Http\Request $request, public \Illuminate\Contracts\Auth\Guard $guard)
    {
    }
}
