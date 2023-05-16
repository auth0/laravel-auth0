<?php

declare(strict_types=1);

namespace Auth0\Laravel;

interface ConfigurationContract
{
    public static function get(
        string $setting,
        array | string | int | bool | null $default = null,
    ): array | string | int | bool | null;

    public static function getEnvironment(): array;

    public static function getJson(): array;

    public static function getPath(): string;

    public static function stringOrIntToIntOrNull(
        int | string $value,
        int | null $default = null,
    ): int | null;

    public static function stringOrNull(
        int | string | null $value,
        string | int | null $default = null,
    ): string | int | null;

    public static function stringToArray(array | string | null $config, string $delimiter = ' '): array;

    /**
     * Converts a delimited string into an array, or null, if nothing was provided.
     *
     * @param null|array<string>|string $config
     * @param string                    $delimiter
     */
    public static function stringToArrayOrNull(array | string | null $config, string $delimiter = ' '): ?array;

    /**
     * Converts a truthy string representation into a boolean.
     *
     * @param null|bool|string $config
     * @param ?bool            $default
     */
    public static function stringToBoolOrNull(string | bool | null $config, ?bool $default = null): ?bool;
}
