<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use function is_string;

/**
 * Assigns a specific guard to the request.
 *
 * @api
 */
final class Guard extends MiddlewareAbstract implements GuardContract
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
            $guard = $this->defaultGuard;
        }

        auth()->shouldUse($guard);

        return $next($request);
    }
}
