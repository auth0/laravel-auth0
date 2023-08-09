<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @api
 */
interface TokenVerificationAttemptingContract extends EventContract
{
    /**
     * @return array{token: string}
     */
    public function jsonSerialize(): array;
}
