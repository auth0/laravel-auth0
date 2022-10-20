<?php

declare(strict_types=1);

namespace Auth0\Laravel;

/**
 * Helpers to map configuration data stored as strings from .env files into formats consumable by the Auth0-PHP SDK.
 */
final class Configuration implements \Auth0\Laravel\Contract\Configuration
{
    /**
     * {@inheritdoc}
     */
    public static function stringToArrayOrNull(?string $config, string $delimiter = ' '): ?array
    {
        if (\is_string($config) && '' !== $config && '' !== $delimiter) {
            $response = explode($delimiter, $config);

            // @phpstan-ignore-next-line
            if (\count($response) >= 1 && '' !== trim($response[0])) {
                return $response;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public static function stringToArray(?string $config, string $delimiter = ' '): array
    {
        if (\is_string($config) && '' !== $config && '' !== $delimiter) {
            $response = explode($delimiter, $config);

            // @phpstan-ignore-next-line
            if (\count($response) >= 1 && '' !== trim($response[0])) {
                return $response;
            }
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function stringToBoolOrNull(?string $config, ?bool $default = null): ?bool
    {
        if (\is_string($config) && '' !== $config) {
            $config = mb_strtolower(trim($config));

            return 'true' === $config;
        }

        return $default;
    }
}
