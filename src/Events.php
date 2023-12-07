<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Auth0\Laravel\Events\Configuration\{
    BuildingConfigurationEvent,
    BuiltConfigurationEvent,
};

use Auth0\Laravel\Events\Middleware\{
    StatefulMiddlewareRequest,
    StatelessMiddlewareRequest,
};

use Auth0\Laravel\Events\{
    AuthenticationFailed,
    AuthenticationSucceeded,
    EventContract,
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
 *
 * @codeCoverageIgnore
 */
final class Events implements EventsContract
{
    /**
     * @var string
     */
    private const TELESCOPE = '\Laravel\Telescope\Telescope';

    public static bool $enabled = true;

    public static function dispatch(EventContract $event): void
    {
        if (self::$enabled) {
            if (self::withoutTelescopeRecording(self::getName($event))) {
                self::TELESCOPE::withoutRecording(static fn (): mixed => event(self::getName($event), $event));

                return;
            }

            event(self::getName($event), $event);
        }
    }

    public static function framework(object $event): void
    {
        if (self::$enabled) {
            if (self::withoutTelescopeRecording($event::class)) {
                self::TELESCOPE::withoutRecording(static fn (): mixed => event($event));

                return;
            }

            event($event);
        }
    }

    private static function getName(EventContract $event): string
    {
        return match ($event::class) {
            AuthenticationFailed::class => self::AUTHENTICATION_FAILED,
            AuthenticationSucceeded::class => self::AUTHENTICATION_SUCCEEDED,
            BuildingConfigurationEvent::class => self::CONFIGURATION_BUILDING,
            BuiltConfigurationEvent::class => self::CONFIGURATION_BUILT,
            LoginAttempting::class => self::LOGIN_ATTEMPTING,
            StatefulMiddlewareRequest::class => self::MIDDLEWARE_STATEFUL_REQUEST,
            StatelessMiddlewareRequest::class => self::MIDDLEWARE_STATELESS_REQUEST,
            TokenExpired::class => self::TOKEN_EXPIRED,
            TokenRefreshFailed::class => self::TOKEN_REFRESH_FAILED,
            TokenRefreshSucceeded::class => self::TOKEN_REFRESH_SUCCEEDED,
            TokenVerificationAttempting::class => self::TOKEN_VERIFICATION_ATTEMPTING,
            TokenVerificationFailed::class => self::TOKEN_VERIFICATION_FAILED,
            TokenVerificationSucceeded::class => self::TOKEN_VERIFICATION_SUCCEEDED,
            default => $event::class,
        };
    }

    private static function withoutTelescopeRecording(string $event): bool
    {
        if (! class_exists(self::TELESCOPE)) {
            return false;
        }

        return match ($event) {
            self::CONFIGURATION_BUILDING => true,
            self::CONFIGURATION_BUILT => true,
            default => false,
        };
    }
}
