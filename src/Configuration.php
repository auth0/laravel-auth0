<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Illuminate\Support\{Arr, Str};

use function constant;
use function count;
use function defined;
use function in_array;
use function is_array;
use function is_bool;
use function is_int;
use function is_string;

/**
 * Helpers to map configuration data stored as strings from .env files into formats consumable by the Auth0-PHP SDK.
 *
 * @api
 */
final class Configuration implements ConfigurationContract
{
    /**
     * @var string[]
     */
    private const USES_ARRAYS = [
        self::CONFIG_AUDIENCE,
        self::CONFIG_SCOPE,
        self::CONFIG_ORGANIZATION,
    ];

    /**
     * @var string[]
     */
    private const USES_BOOLEANS = [
        self::CONFIG_USE_PKCE,
        self::CONFIG_HTTP_TELEMETRY,
        self::CONFIG_COOKIE_SECURE,
        self::CONFIG_PUSHED_AUTHORIZATION_REQUEST,
    ];

    /**
     * @var string[]
     */
    private const USES_INTEGERS = [
        self::CONFIG_TOKEN_MAX_AGE,
        self::CONFIG_TOKEN_LEEWAY,
        self::CONFIG_TOKEN_CACHE_TTL,
        self::CONFIG_HTTP_MAX_RETRIES,
        self::CONFIG_COOKIE_EXPIRES,
        self::CONFIG_BACKCHANNEL_LOGOUT_EXPIRES,
    ];

    /**
     * @var string
     */
    public const CONFIG_AUDIENCE = 'audience';

    /**
     * @var string
     */
    public const CONFIG_BACKCHANNEL_LOGOUT_CACHE = 'backchannelLogoutCache';

    /**
     * @var string
     */
    public const CONFIG_BACKCHANNEL_LOGOUT_EXPIRES = 'backchannelLogoutExpires';

    /**
     * @var string
     */
    public const CONFIG_CLIENT_ASSERTION_SIGNING_ALGORITHM = 'clientAssertionSigningAlgorithm';

    /**
     * @var string
     */
    public const CONFIG_CLIENT_ASSERTION_SIGNING_KEY = 'clientAssertionSigningKey';

    /**
     * @var string
     */
    public const CONFIG_CLIENT_ID = 'clientId';

    /**
     * @var string
     */
    public const CONFIG_CLIENT_SECRET = 'clientSecret';

    /**
     * @var string
     */
    public const CONFIG_COOKIE_DOMAIN = 'cookieDomain';

    /**
     * @var string
     */
    public const CONFIG_COOKIE_EXPIRES = 'cookieExpires';

    /**
     * @var string
     */
    public const CONFIG_COOKIE_PATH = 'cookiePath';

    /**
     * @var string
     */
    public const CONFIG_COOKIE_SAME_SITE = 'cookieSameSite';

    /**
     * @var string
     */
    public const CONFIG_COOKIE_SECRET = 'cookieSecret';

    /**
     * @var string
     */
    public const CONFIG_COOKIE_SECURE = 'cookieSecure';

    /**
     * @var string
     */
    public const CONFIG_CUSTOM_DOMAIN = 'customDomain';

    /**
     * @var string
     */
    public const CONFIG_DOMAIN = 'domain';

    /**
     * @var string
     */
    public const CONFIG_HTTP_MAX_RETRIES = 'httpMaxRetries';

    /**
     * @var string
     */
    public const CONFIG_HTTP_TELEMETRY = 'httpTelemetry';

    /**
     * @var string
     */
    public const CONFIG_MANAGEMENT_TOKEN = 'managementToken';

    /**
     * @var string
     */
    public const CONFIG_MANAGEMENT_TOKEN_CACHE = 'managementTokenCache';

    /**
     * @var string
     */
    public const CONFIG_NAMESPACE = 'auth0.';

    /**
     * @var string
     */
    public const CONFIG_NAMESPACE_ROUTES = 'auth0.routes.';

    /**
     * @var string
     */
    public const CONFIG_ORGANIZATION = 'organization';

    /**
     * @var string
     */
    public const CONFIG_PUSHED_AUTHORIZATION_REQUEST = 'pushedAuthorizationRequest';

    /**
     * @var string
     */
    public const CONFIG_REDIRECT_URI = 'redirectUri';

    /**
     * @var string
     */
    public const CONFIG_RESPONSE_MODE = 'responseMode';

    /**
     * @var string
     */
    public const CONFIG_RESPONSE_TYPE = 'responseType';

    /**
     * @var string
     */
    public const CONFIG_ROUTE_AFTER_LOGIN = 'afterLogin';

    /**
     * @var string
     */
    public const CONFIG_ROUTE_AFTER_LOGOUT = 'afterLogout';

    /**
     * @var string
     */
    public const CONFIG_ROUTE_BACKCHANNEL = 'backchannel';

    /**
     * @var string
     */
    public const CONFIG_ROUTE_CALLBACK = 'callback';

    /**
     * @var string
     */
    public const CONFIG_ROUTE_INDEX = 'index';

    /**
     * @var string
     */
    public const CONFIG_ROUTE_LOGIN = 'login';

    /**
     * @var string
     */
    public const CONFIG_ROUTE_LOGOUT = 'logout';

    /**
     * @var string
     */
    public const CONFIG_SCOPE = 'scope';

    /**
     * @var string
     */
    public const CONFIG_SESSION_STORAGE = 'sessionStorage';

    /**
     * @var string
     */
    public const CONFIG_SESSION_STORAGE_ID = 'sessionStorageId';

    /**
     * @var string
     */
    public const CONFIG_STRATEGY = 'strategy';

    /**
     * @var string
     */
    public const CONFIG_TOKEN_ALGORITHM = 'tokenAlgorithm';

    /**
     * @var string
     */
    public const CONFIG_TOKEN_CACHE = 'tokenCache';

    /**
     * @var string
     */
    public const CONFIG_TOKEN_CACHE_TTL = 'tokenCacheTtl';

    /**
     * @var string
     */
    public const CONFIG_TOKEN_JWKS_URI = 'tokenJwksUri';

    /**
     * @var string
     */
    public const CONFIG_TOKEN_LEEWAY = 'tokenLeeway';

    /**
     * @var string
     */
    public const CONFIG_TOKEN_MAX_AGE = 'tokenMaxAge';

    /**
     * @var string
     */
    public const CONFIG_TRANSIENT_STORAGE = 'transientStorage';

    /**
     * @var string
     */
    public const CONFIG_TRANSIENT_STORAGE_ID = 'transientStorageId';

    /**
     * @var string
     */
    public const CONFIG_USE_PKCE = 'usePkce';

    /**
     * @var array<string, int>
     */
    public const VERSION_2 = ['AUTH0_CONFIG_VERSION' => 2];

    private static ?array $environment = null;

    private static ?array $json = null;

    private static ?string $path = null;

    public static function get(
        string $setting,
        array | string | int | bool | null $default = null,
    ): array | string | int | bool | null {
        if (in_array($setting, self::USES_ARRAYS, true)) {
            $value = self::getValue($setting, $default);

            if (! is_string($value)) {
                return $default;
            }

            return self::stringToArrayOrNull($value, ',') ?? $default;
        }

        if (in_array($setting, self::USES_BOOLEANS, true)) {
            $value = self::getValue($setting, $default);

            if (! is_bool($value) && ! is_string($value)) {
                return $default;
            }

            return self::stringToBoolOrNull($value) ?? $default;
        }

        if (in_array($setting, self::USES_INTEGERS, true)) {
            $value = self::getValue($setting, $default);

            if (! is_int($value) && ! is_string($value)) {
                return $default;
            }

            return self::stringOrIntToIntOrNull($value) ?? $default;
        }

        $result = null;
        $value = self::getValue($setting) ?? $default;

        if (is_string($value) || is_int($value) || null === $value) {
            $result = self::stringOrNull($value) ?? $default;
        }

        if (self::CONFIG_DOMAIN === $setting && null === $result) {
            // Fallback to extracting the tenant domain from the signing key subject.
            $temp = self::getJson()['signing_keys.0.subject'] ?? '';
            $temp = explode('=', $temp);

            if (isset($temp[1]) && str_ends_with($temp[1], '.auth0.com')) {
                $result = $temp[1]; // @codeCoverageIgnore
            }
        }

        return $result ?? $default;
    }

    /**
     * @codeCoverageIgnore
     *
     * @psalm-suppress DocblockTypeContradiction
     */
    public static function getEnvironment(): array
    {
        if (null === self::$environment) {
            $path = self::getPath();
            $laravelEnvironment = env('APP_ENV');
            $laravelEnvironment = is_string($laravelEnvironment) && '' !== trim($laravelEnvironment) ? trim($laravelEnvironment) : 'local';

            $env = [];
            $files = ['.env', '.env.auth0'];
            $files[] = '.env.' . $laravelEnvironment;
            $files[] = '.env.auth0.' . $laravelEnvironment;

            foreach ($files as $file) {
                if (! file_exists($path . $file)) {
                    continue;
                }

                $contents = file($path . $file, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);

                if (! is_array($contents)) {
                    continue;
                }

                if ([] === $contents) {
                    continue;
                }

                foreach ($contents as $content) {
                    if (1 !== substr_count($content, '=')) {
                        continue;
                    }

                    [$k,$v] = explode('=', $content);

                    // @phpstan-ignore-next-line
                    if (! is_string($k)) {
                        continue;
                    }

                    // @phpstan-ignore-next-line
                    if (! is_string($v)) {
                        continue;
                    }

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

    /**
     * @codeCoverageIgnore
     */
    public static function getJson(): array
    {
        if (null === self::$json) {
            $path = self::getPath();
            $laravelEnvironment = env('APP_ENV');
            $laravelEnvironment = is_string($laravelEnvironment) && '' !== trim($laravelEnvironment) ? trim($laravelEnvironment) : 'local';

            $configuration = [];
            $files = ['.auth0.json', '.auth0.api.json', '.auth0.app.json'];
            $files[] = '.auth0.' . $laravelEnvironment . '.json';
            $files[] = '.auth0.' . $laravelEnvironment . '.api.json';
            $files[] = '.auth0.' . $laravelEnvironment . '.app.json';

            foreach ($files as $file) {
                if (file_exists($path . $file)) {
                    $content = file_get_contents($path . $file);

                    if (is_string($content)) {
                        $json = json_decode($content, true, 512);

                        if (is_array($json)) {
                            $configuration = array_merge($configuration, $json);
                        }
                    }
                }
            }

            self::$json = Arr::dot($configuration);
        }

        return self::$json;
    }

    public static function getPath(): string
    {
        if (null === self::$path) {
            $path = config('auth0.configurationPath');

            if (! is_string($path)) {
                $path = base_path() . DIRECTORY_SEPARATOR;
            }

            self::$path = $path;
        }

        return self::$path;
    }

    public static function string(string $key, ?string $default = null): ?string
    {
        $value = config($key, $default);

        if (is_string($value)) {
            return $value;
        }

        return null;
    }

    public static function stringOrIntToIntOrNull(
        int | string $value,
        int | null $default = null,
    ): int | null {
        if (is_int($value)) {
            return $value;
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
        string | int | null $value,
        string | int | null $default = null,
    ): string | int | null {
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

    public static function stringToArray(array | string | null $config, string $delimiter = ' '): array
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

    public static function stringToArrayOrNull(array | string | null $config, string $delimiter = ' '): ?array
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

    public static function stringToBoolOrNull(bool | string | null $config, ?bool $default = null): ?bool
    {
        if (is_bool($config)) {
            return $config;
        }

        if (is_string($config) && '' !== $config) {
            $config = strtolower(trim($config));

            if ('true' === $config) {
                return true;
            }

            if ('false' === $config) {
                return false;
            }
        }

        return $default;
    }

    public static function version(): int
    {
        $version = config('auth0.AUTH0_CONFIG_VERSION', 1);

        return is_int($version) ? $version : 1;
    }

    private static function getValue(
        string $setting,
        array | bool | string | int | null $default = null,
    ): array | bool | string | int | null {
        $value = null;

        if (defined('AUTH0_OVERRIDE_CONFIGURATION')) {
            $override = constant('AUTH0_OVERRIDE_CONFIGURATION');

            if (is_string($override)) {
                $value = config($override . '.' . $setting);
            }
        } else {
            $env = 'AUTH0_' . strtoupper(Str::snake($setting));
            $json = self::CONFIG_AUDIENCE === $setting ? 'identifier' : Str::snake($setting);

            $value = getenv($env);

            if (! is_string($value)) {
                $value = null;
            }

            $value ??= self::getEnvironment()[$env] ?? null;

            $value = match ($setting) {
                self::CONFIG_DOMAIN => '{DOMAIN}' === $value ? null : $value,
                self::CONFIG_CLIENT_ID => '{CLIENT_ID}' === $value ? null : $value,
                self::CONFIG_CLIENT_SECRET => '{CLIENT_SECRET}' === $value ? null : $value,
                self::CONFIG_AUDIENCE => '{API_IDENTIFIER}' === $value ? null : $value,
                default => $value,
            };

            $value ??= self::getJson()[$json] ?? $default;
        }

        if (! is_string($value) && ! is_array($value) && ! is_bool($value) && ! is_int($value)) {
            $value = null;
        }

        if (is_string($value)) {
            $value = trim($value, '\'"');
        }

        return $value ?? $default;
    }
}
