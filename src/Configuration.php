<?php

declare(strict_types=1);

namespace Auth0\Laravel;

/**
 * Helpers to map configuration data stored as strings from .env files into formats consumable by the Auth0-PHP SDK.
 */
final class Configuration implements \Auth0\Laravel\Contract\Configuration
{
    /**
     * @inheritdoc
     */
    public static function stringToArrayOrNull(
        ?string $config,
        string $delimiter = ' ',
    ): ?array {
        if (is_string($config) === true && strlen($config) >= 1 && strlen($delimiter) >= 1) {
            $response = explode($delimiter, $config);

            // @phpstan-ignore-next-line
            if (is_array($response) === true && count($response) >= 1 && strlen(trim($response[0])) !== '') {
                return $response;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public static function stringToBoolOrNull(
        ?string $config,
        ?bool $default = null
    ): ?bool {
        if (is_string($config) === true && strlen($config) >= 1) {
            $config = strtolower(trim($config));

            if ($config === 'true') {
                return true;
            }

            return false;
        }

        return $default;
    }
}
