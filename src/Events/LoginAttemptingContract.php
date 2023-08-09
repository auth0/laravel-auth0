<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @api
 */
interface LoginAttemptingContract extends EventContract
{
    /**
     * @return array{parameters: array<array-key, mixed>}
     */
    public function jsonSerialize(): array;
}
