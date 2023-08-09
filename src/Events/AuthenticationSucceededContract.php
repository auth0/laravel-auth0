<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @api
 */
interface AuthenticationSucceededContract extends EventContract
{
    /**
     * @return array{user: array<array-key, mixed>}
     */
    public function jsonSerialize(): array;
}
