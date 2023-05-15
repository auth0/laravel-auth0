<?php

declare(strict_types=1);

namespace Auth0\Laravel;

interface ConfigurationContract
{
    /**
     * Converts a delimited string into an array, or null, if nothing was provided.
     */
    public static function stringToArrayOrNull(?string $config, string $delimiter = ' '): ?array;

    /**
     * Converts a truthy string representation into a boolean.
     */
    public static function stringToBoolOrNull(?string $config, ?bool $default = null): ?bool;

    public static function get(
        string $setting,
        array | string | int | bool | null $default = null,
    ): array | string | int | bool | null;

    public static function getEnvironment(): array;

    public static function getJson(): array;

    public static function stringOrIntToIntOrNull(
        mixed $value,
        int | null $default = null,
    ): int | null;

    public static function stringOrNull(
        mixed $value,
        string | int | null $default = null,
    ): string | int | null;

    public static function stringToArray(array | string | null $config, string $delimiter = ' '): array;

    public static function getPath(): string;
}
