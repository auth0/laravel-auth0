<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @codeCoverageIgnore
 */
abstract class MiddlewareAbstract
{
    abstract public function handle(
        Request $request,
        Closure $next,
        string $scope = '',
    ): Response;
}
