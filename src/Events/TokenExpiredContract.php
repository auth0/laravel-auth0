<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @api
 */
interface TokenExpiredContract extends EventContract
{
    public function jsonSerialize(): ?array;
}
