<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Auth0\Laravel\Contract\Configuration as ConfigurationContract;
use Illuminate\Support\{Arr, Str};

use function count;
use function is_string;

/**
 * Helpers to map configuration data stored as strings from .env files into formats consumable by the Auth0-PHP SDK.
 */
final class Configuration implements ConfigurationContract
{
    private static ?array $environment = null;
    private static ?array $json = null;
    private static ?string $path = null;

    PUBLIC CONST VERSION_2 = ['AUTH0_CONFIG_VERSION' => 2];

    public const CONFIG_STRATEGY = 'strategy';
    public const CONFIG_DOMAIN = 'domain';
    public const CONFIG_CUSTOM_DOMAIN = 'customDomain';
    public const CONFIG_CLIENT_ID = 'clientId';
    public const CONFIG_REDIRECT_URI = 'redirectUri';
    public const CONFIG_CLIENT_SECRET = 'clientSecret';
    public const CONFIG_AUDIENCE = 'audience';
    public const CONFIG_ORGANIZATION = 'organization';
    public const CONFIG_USE_PKCE = 'usePkce';
    public const CONFIG_SCOPE = 'scope';
    public const CONFIG_RESPONSE_MODE = 'responseMode';
    public const CONFIG_RESPONSE_TYPE = 'responseType';
    public const CONFIG_TOKEN_ALGORITHM = 'tokenAlgorithm';
    public const CONFIG_TOKEN_JWKS_URI = 'tokenJwksUri';
    public const CONFIG_TOKEN_MAX_AGE = 'tokenMaxAge';
    public const CONFIG_TOKEN_LEEWAY = 'tokenLeeway';
    public const CONFIG_TOKEN_CACHE = 'tokenCache';
    public const CONFIG_TOKEN_CACHE_TTL = 'tokenCacheTtl';
    public const CONFIG_HTTP_MAX_RETRIES = 'httpMaxRetries';
    public const CONFIG_HTTP_TELEMETRY = 'httpTelemetry';
    public const CONFIG_SESSION_STORAGE = 'sessionStorage';
    public const CONFIG_SESSION_STORAGE_ID = 'sessionStorageId';
    public const CONFIG_COOKIE_SECRET = 'cookieSecret';
    public const CONFIG_COOKIE_DOMAIN = 'cookieDomain';
    public const CONFIG_COOKIE_EXPIRES = 'cookieExpires';
    public const CONFIG_COOKIE_PATH = 'cookiePath';
    public const CONFIG_COOKIE_SECURE = 'cookieSecure';
    public const CONFIG_COOKIE_SAME_SITE = 'cookieSameSite';
    public const CONFIG_TRANSIENT_STORAGE = 'transientStorage';
    public const CONFIG_TRANSIENT_STORAGE_ID = 'transientStorageId';
    public const CONFIG_MANAGEMENT_TOKEN = 'managementToken';
    public const CONFIG_MANAGEMENT_TOKEN_CACHE = 'managementTokenCache';
    public const CONFIG_CLIENT_ASSERTION_SIGNING_KEY = 'clientAssertionSigningKey';
    public const CONFIG_CLIENT_ASSERTION_SIGNING_ALGORITHM = 'clientAssertionSigningAlgorithm';
    public const CONFIG_PUSHED_AUTHORIZATION_REQUEST = 'pushedAuthorizationRequest';

    public static function get(
        string $setting,
        array|string|int|bool|null $default = null
    ): array|string|int|bool|null {
        $usesArrays = [
            self::CONFIG_AUDIENCE,
            self::CONFIG_SCOPE,
            self::CONFIG_ORGANIZATION,
        ];

        if (in_array($setting, $usesArrays, true)) {
            return self::stringToArrayOrNull(self::getValue($setting, $default), ',');
        }

        $usesBooleans = [
            self::CONFIG_USE_PKCE,
            self::CONFIG_HTTP_TELEMETRY,
            self::CONFIG_COOKIE_SECURE,
            self::CONFIG_PUSHED_AUTHORIZATION_REQUEST,
        ];

        if (in_array($setting, $usesBooleans, true)) {
            return self::stringToBoolOrNull(self::getValue($setting, $default), $default);
        }

        $usesIntegers = [
            self::CONFIG_TOKEN_MAX_AGE,
            self::CONFIG_TOKEN_LEEWAY,
            self::CONFIG_TOKEN_CACHE_TTL,
            self::CONFIG_HTTP_MAX_RETRIES,
            self::CONFIG_COOKIE_EXPIRES
        ];

        if (in_array($setting, $usesIntegers, true)) {
            return self::stringOrIntToIntOrNull(self::getValue($setting, $default), $default);
        }

        $result = self::stringOrNull(self::getValue($setting, $default), $default);

        if ($setting === self::CONFIG_DOMAIN && null === $result) {
            // Fallback to extracting the tenant domain from the signing key subject.
            $result = self::getJson()['signing_keys.0.subject'] ?? '';
            $result = explode('=', $result);

            if (count($result) >= 1 && str_ends_with($result[1] ?? '', '.auth0.com')) {
                return $result[1];
            }
        }

        return $result;
    }

    private static function getValue(
        string $setting,
        array|bool|string|int|null $default = null
    ): array|bool|string|int|null {
        $env ??= 'AUTH0_' . strtoupper(Str::snake($setting));
        $json = Str::snake($setting);

        if ($setting === self::CONFIG_AUDIENCE) {
            $json = 'identifier';
        } else {
            $json = Str::snake($setting);
        }

        $value = self::getEnvironment()[$env] ?? self::getJson()[$json] ?? $default;

        if (is_string($value) || is_int($value)) {
            return $value;
        }

        return $default;
    }

    public static function stringOrIntToIntOrNull(
        mixed $value,
        int|null $default = null
    ): int|null {
        if (is_int($value)) {
            return $value;
        }

        if (! is_string($value)) {
            return $default;
        }

        $value = trim($value);

        if ('' === $value) {
            return $default;
        }

        if (ctype_digit($value)) {
            return (int) $value;
        }

        return $default;
    }

    public static function stringOrNull(
        mixed $value,
        string|int|null $default = null
    ): string|int|null {
        if (! is_string($value)) {
            return $default;
        }

        $value = trim($value);

        if ('' === $value) {
            return $default;
        }

        if ('empty' === $value) {
            return $default;
        }

        if ('(empty)' === $value) {
            return $default;
        }

        if ('null' === $value) {
            return $default;
        }

        if ('(null)' === $value) {
            return $default;
        }

        return $value;
    }

    public static function stringToArray(array|string|null $config, string $delimiter = ' '): array
    {
        if (is_array($config)) {
            return $config;
        }

        if (is_string($config) && '' !== $config && '' !== $delimiter) {
            $response = explode($delimiter, $config);

            // @phpstan-ignore-next-line
            if (count($response) >= 1 && '' !== trim($response[0])) {
                return $response;
            }
        }

        return [];
    }

    public static function stringToArrayOrNull(array|string|null $config, string $delimiter = ' '): ?array
    {
        if (is_array($config) && [] !== $config) {
            return $config;
        }

        if (is_string($config) && '' !== $config && '' !== $delimiter) {
            $response = explode($delimiter, $config);

            // @phpstan-ignore-next-line
            if (count($response) >= 1 && '' !== trim($response[0])) {
                return $response;
            }
        }

        return null;
    }

    public static function stringToBoolOrNull(bool|string|null $config, ?bool $default = null): ?bool
    {
        if (is_bool($config)) {
            return $config;
        }

        if (is_string($config) && '' !== $config) {
            $config = mb_strtolower(trim($config));

            if ('true' === $config) {
                return true;
            }

            if ('false' === $config) {
                return false;
            }
        }

        return $default;
    }

    private static function getPath(): string {
        if (self::$path === null) {
            self::$path = base_path() . DIRECTORY_SEPARATOR;
        }

        return self::$path;
    }

    public static function getEnvironment(): array {
        if (self::$environment === null) {
            $path = self::getPath();

            $env = [];
            $files = ['.env', '.env.auth0'];

            if (null !== env('APP_ENV')) {
                $files[] = '.env.' . env('APP_ENV');
                $files[] = '.env.auth0.' . env('APP_ENV');
            }

            foreach($files as $file) {
                if (! file_exists($path . $file)) {
                    continue;
                }

                $contents = file($path . $file, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);

                if (! is_array($contents) || [] === $contents) {
                    continue;
                }

                foreach($contents as $line) {
                    [$k,$v] = explode('=', $line);
                    $v = trim($v);

                    if ('' === $v) {
                        $v = null;
                    } elseif ('empty' === $v) {
                        $v = null;
                    } elseif ('(empty)' === $v) {
                        $v = null;
                    } elseif ('null' === $v) {
                        $v = null;
                    } elseif ('(null)' === $v) {
                        $v = null;
                    } elseif ('true' === $v) {
                        $v = true;
                    } elseif ('(true)' === $v) {
                        $v = true;
                    } elseif ('false' === $v) {
                        $v = false;
                    } elseif ('(false)' === $v) {
                        $v = false;
                    }

                    $env[trim($k)] = $v;
                }
            }

            self::$environment = $env;
        }

        return self::$environment;
    }

    public static function getJson(): array {
        if (self::$json === null) {
            $path = self::getPath();

            $configuration = [];
            $files = ['.auth0.json', '.auth0.api.json', '.auth0.app.json'];

            if (null !== env('APP_ENV')) {
                $files[] = '.auth0.' . env('APP_ENV') . '.json';
                $files[] = '.auth0.' . env('APP_ENV') . '.api.json';
                $files[] = '.auth0.' . env('APP_ENV') . '.app.json';
            }

            foreach ($files as $file) {
                if (file_exists($path . $file)) {
                    $json = json_decode(file_get_contents($path . $file), true, 512);

                    if (is_array($json)) {
                        $configuration = array_merge($configuration, $json);
                    }
                }
            }

            self::$json = Arr::dot($configuration);
        }

        return self::$json;
    }
}
