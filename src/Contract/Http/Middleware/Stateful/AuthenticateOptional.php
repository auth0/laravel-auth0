<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Http\Middleware\Stateful;

interface AuthenticateOptional
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, \Closure $next);
}
