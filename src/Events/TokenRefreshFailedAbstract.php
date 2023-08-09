<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @internal
 *
 * @api
 */
abstract class TokenRefreshFailedAbstract extends EventAbstract
{
    final public function jsonSerialize(): ?array
    {
        return null;
    }
}
