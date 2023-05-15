<?php

declare(strict_types=1);

namespace Auth0\Laravel\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @codeCoverageIgnore
 *
 * @api
 */
interface AuthenticateMiddlewareContract extends MiddlewareContract
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string  $scope
     */
    public function handle(
        Request $request,
        Closure $next,
        string $scope = '',
    ): Response;
}
