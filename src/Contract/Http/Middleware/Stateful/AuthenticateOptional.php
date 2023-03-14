<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Http\Middleware\Stateful;

use Closure;
use Illuminate\Http\{JsonResponse, Request, Response};
use Illuminate\Routing\Redirector;

interface AuthenticateOptional
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     */
    public function handle(Request $request, Closure $next): Response | Redirector | JsonResponse;
}
