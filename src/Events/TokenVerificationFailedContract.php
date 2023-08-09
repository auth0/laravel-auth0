<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @api
 */
interface TokenVerificationFailedContract extends EventContract
{
    /**
     * @return array{token: string, exception: array<array-key, mixed>, throwException: bool}
     */
    public function jsonSerialize(): array;
}
