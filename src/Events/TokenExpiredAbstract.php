<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @internal
 *
 * @api
 */
abstract class TokenExpiredAbstract extends EventAbstract
{
    final public function jsonSerialize(): ?array
    {
        return null;
    }
}
