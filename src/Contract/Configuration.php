<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract;

interface Configuration
{
    /**
     * Converts a delimited string into an array, or null, if nothing was provided.
     *
     * @param  string|null  $config  The string contents to convert, i.e. 'one two three'.
     * @param  string  $delimiter  the string delimiter to split the string contents with; defaults to space
     */
    public static function stringToArrayOrNull(?string $config, string $delimiter = ' '): ?array;

    /**
     * Converts a truthy string representation into a boolean.
     *
     * @param  string|null  $config  The string contents to convert, i.e. 'true'
     * @param  bool|null  $default  the default boolean value to return if a valid string wasn't provided
     */
    public static function stringToBoolOrNull(?string $config, ?bool $default = null): ?bool;
}
