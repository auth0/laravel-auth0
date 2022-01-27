<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Http\Middleware\Stateless;

interface Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string                   $scope
     *
     * @return mixed
     */
    public function handle(
        \Illuminate\Http\Request $request,
        \Closure $next,
        string $scope
    );
}
