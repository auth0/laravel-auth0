<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @api
 */
interface TokenRefreshFailedContract extends EventContract
{
    public function jsonSerialize(): ?array;
}
