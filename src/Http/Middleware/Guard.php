<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use function is_string;

/**
 * This helper middleware assigns a specific guard for use in a routing group.
 * Note that this middleware is not required for the Auth0 Laravel SDK to function,
 * but can be used to simplify configuration of multiple guards.
 */
final class Guard
{
    private string $defaultGuard = '';

    public function __construct()
    {
        $guard = config('auth.defaults.guard');

        if (is_string($guard)) {
            $this->defaultGuard = $guard;
        }
    }

    public function handle(
        Request $request,
        Closure $next,
        ?string $guard = null,
    ): Response {
        $guard = trim($guard ?? '');

        if ('' === $guard) {
            auth()->shouldUse($this->defaultGuard);

            return $next($request);
        }

        auth()->shouldUse($guard);

        return $next($request);
    }
}
