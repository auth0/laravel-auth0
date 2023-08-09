<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @api
 */
interface AuthenticationFailedContract extends EventContract
{
    /**
     * @return array{exception: array<array-key, mixed>, throwException: bool}
     */
    public function jsonSerialize(): array;
}
