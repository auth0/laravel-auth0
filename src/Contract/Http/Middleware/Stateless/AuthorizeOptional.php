<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Http\Middleware\Stateless;

use Closure;
use Illuminate\Http\{JsonResponse, Request, Response};

interface AuthorizeOptional
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     */
    public function handle(Request $request, Closure $next): Response | JsonResponse;
}
