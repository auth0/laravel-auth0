<?php

declare(strict_types=1);

namespace Auth0\Laravel\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated 7.8.0 This middleware is no longer necessary when using Auth0\Laravel\Guards\AuthorizationGuard. Use Laravel's standard `auth` middleware instead.
 *
 * @api
 */
interface AuthorizeMiddlewareContract extends MiddlewareContract
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
