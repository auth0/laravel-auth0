<?php

declare(strict_types=1);

namespace Auth0\Laravel;

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

use Auth0\Laravel\Events\Configuration\{
    BuildingConfigurationEvent,
    BuiltConfigurationEvent,
};

use Auth0\Laravel\Events\EventContract;

use Auth0\Laravel\Events\Middleware\{
    StatefulMiddlewareRequest,
    StatelessMiddlewareRequest,
};


/**
 * @api
 */
interface EventsContract
{
    /**
     * @var class-string<AuthenticationFailed>
     */
    public const AUTHENTICATION_FAILED = AuthenticationFailed::class;

    /**
     * @var class-string<AuthenticationSucceeded>
     */
    public const AUTHENTICATION_SUCCEEDED = AuthenticationSucceeded::class;

    /**
     * @var class-string<BuildingConfigurationEvent>
     */
    public const CONFIGURATION_BUILDING = BuildingConfigurationEvent::class;

    /**
     * @var class-string<BuiltConfigurationEvent>
     */
    public const CONFIGURATION_BUILT = BuiltConfigurationEvent::class;

    /**
     * @var class-string<LoginAttempting>
     */
    public const LOGIN_ATTEMPTING = LoginAttempting::class;

    /**
     * @var class-string<StatefulMiddlewareRequest>
     */
    public const MIDDLEWARE_STATEFUL_REQUEST = StatefulMiddlewareRequest::class;

    /**
     * @var class-string<StatelessMiddlewareRequest>
     */
    public const MIDDLEWARE_STATELESS_REQUEST = StatelessMiddlewareRequest::class;

    /**
     * @var class-string<TokenExpired>
     */
    public const TOKEN_EXPIRED = TokenExpired::class;

    /**
     * @var class-string<TokenRefreshFailed>
     */
    public const TOKEN_REFRESH_FAILED = TokenRefreshFailed::class;

    /**
     * @var class-string<TokenRefreshSucceeded>
     */
    public const TOKEN_REFRESH_SUCCEEDED = TokenRefreshSucceeded::class;

    /**
     * @var class-string<TokenVerificationAttempting>
     */
    public const TOKEN_VERIFICATION_ATTEMPTING = TokenVerificationAttempting::class;

    /**
     * @var class-string<TokenVerificationFailed>
     */
    public const TOKEN_VERIFICATION_FAILED = TokenVerificationFailed::class;

    /**
     * @var class-string<TokenVerificationSucceeded>
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
