<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @api
 */
interface TokenRefreshSucceededContract extends EventContract
{
    public function jsonSerialize(): ?array;
}
