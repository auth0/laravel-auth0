<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Illuminate\Auth\AuthManager;
use Illuminate\Routing\Router;

/**
 * @api
 */
interface ServiceProviderContract
{
    /**
     * Register migration helpers for deprecated classes.
     *
     * @param Router      $router
     * @param AuthManager $auth
     */
    public function registerDeprecated(
        Router $router,
        AuthManager $auth,
    ): void;

    /**
     * Register the Auth0 guards.
     */
    public function registerGuards(): void;

    /**
     * Register the Auth0 service middleware.
     *
     * @param Router $router
     */
    public function registerMiddleware(
        Router $router,
    ): void;

    /**
     * Register the Auth0 authentication routes.
     */
    public function registerRoutes(): void;
}
