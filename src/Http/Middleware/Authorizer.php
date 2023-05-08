<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class Authorizer
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        auth()->shouldUse('auth0-api');
        return $next($request);
    }
}
