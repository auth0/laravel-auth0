<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateless;

use Auth0\Laravel\Events\EventAbstract;
use Auth0\Laravel\Events\TokenVerificationFailedContract;
use Throwable;

/**
 * Raised when a token has failed verification.
 *
 * @api
 */
final class TokenVerificationFailed extends EventAbstract implements TokenVerificationFailedContract
{
    public function __construct(
        private string $token,
        private Throwable $exception,
    ) {
    }

    public function getException(): Throwable
    {
        return $this->exception;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
