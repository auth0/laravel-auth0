<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @api
 */
interface TokenVerificationSucceededContract extends EventContract
{
    /**
     * @return array{token: string, payload: array<array-key, mixed>}
     */
    public function jsonSerialize(): array;
}
