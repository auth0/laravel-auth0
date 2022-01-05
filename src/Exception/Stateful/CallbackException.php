<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exception\Stateful;

/**
 * @codeCoverageIgnore
 */
final class CallbackException extends \Exception implements \Auth0\SDK\Exception\Auth0Exception
{
    public const MSG_API_RESPONSE = '%s: %s';

    public static function apiException(
        string $error,
        string $errorDescription
    ): self {
        return new self(sprintf(self::MSG_API_RESPONSE, $error, $errorDescription));
    }
}
