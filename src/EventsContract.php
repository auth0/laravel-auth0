<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Auth0\Laravel\Events\Configuration\{
    BuildingConfigurationEvent,
    BuiltConfigurationEvent,
};

use Auth0\Laravel\Events\EventContract;

use Auth0\Laravel\Events\Middleware\{
    StatefulMiddlewareRequest,
    StatelessMiddlewareRequest,
};

use Auth0\Laravel\Events\{
    AuthenticationFailed,
    AuthenticationSucceeded,
    LoginAttempting,
    TokenExpired,
    TokenRefreshFailed,
    TokenRefreshSucceeded,
    TokenVerificationAttempting,
    TokenVerificationFailed,
    TokenVerificationSucceeded,
};

/**
 * @api
 */
interface EventsContract
{
    /**
     * @var class-string<\Auth0\Laravel\Events\AuthenticationFailed>
     */
    public const AUTHENTICATION_FAILED = AuthenticationFailed::class;

    /**
     * @var class-string<\Auth0\Laravel\Events\AuthenticationSucceeded>
     */
    public const AUTHENTICATION_SUCCEEDED = AuthenticationSucceeded::class;

    /**
     * @var class-string<\Auth0\Laravel\Events\Configuration\BuildingConfigurationEvent>
     */
    public const CONFIGURATION_BUILDING = BuildingConfigurationEvent::class;

    /**
     * @var class-string<\Auth0\Laravel\Events\Configuration\BuiltConfigurationEvent>
     */
    public const CONFIGURATION_BUILT = BuiltConfigurationEvent::class;

    /**
     * @var class-string<\Auth0\Laravel\Events\LoginAttempting>
     */
    public const LOGIN_ATTEMPTING = LoginAttempting::class;

    /**
     * @var class-string<\Auth0\Laravel\Events\Middleware\StatefulMiddlewareRequest>
     */
    public const MIDDLEWARE_STATEFUL_REQUEST = StatefulMiddlewareRequest::class;

    /**
     * @var class-string<\Auth0\Laravel\Events\Middleware\StatelessMiddlewareRequest>
     */
    public const MIDDLEWARE_STATELESS_REQUEST = StatelessMiddlewareRequest::class;

    /**
     * @var class-string<\Auth0\Laravel\Events\TokenExpired>
     */
    public const TOKEN_EXPIRED = TokenExpired::class;

    /**
     * @var class-string<\Auth0\Laravel\Events\TokenRefreshFailed>
     */
    public const TOKEN_REFRESH_FAILED = TokenRefreshFailed::class;

    /**
     * @var class-string<\Auth0\Laravel\Events\TokenRefreshSucceeded>
     */
    public const TOKEN_REFRESH_SUCCEEDED = TokenRefreshSucceeded::class;

    /**
     * @var class-string<\Auth0\Laravel\Events\TokenVerificationAttempting>
     */
    public const TOKEN_VERIFICATION_ATTEMPTING = TokenVerificationAttempting::class;

    /**
     * @var class-string<\Auth0\Laravel\Events\TokenVerificationFailed>
     */
    public const TOKEN_VERIFICATION_FAILED = TokenVerificationFailed::class;

    /**
     * @var class-string<\Auth0\Laravel\Events\TokenVerificationSucceeded>
     */
    public const TOKEN_VERIFICATION_SUCCEEDED = TokenVerificationSucceeded::class;

    /**
     * Dispatch an SDK event.
     *
     * @param EventContract $event The event to dispatch.
     */
    public static function dispatch(
        EventContract $event,
    ): void;

    /**
     * Dispatch a Laravel framework event.
     *
     * @param object $event The event to dispatch.
     */
    public static function framework(
        object $event,
    ): void;
}
