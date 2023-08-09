<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

use Throwable;

/**
 * @internal
 *
 * @api
 */
abstract class TokenVerificationFailedAbstract extends EventAbstract
{
    /**
     * @param string    $token          Encoded JSON Web Token that failed verification.
     * @param Throwable $exception      Exception to be thrown following this event.
     * @param bool      $throwException Whether or not $exception will be thrown
     */
    public function __construct(
        public string $token,
        public Throwable &$exception,
        public bool $throwException = false,
    ) {
    }

    /**
     * @psalm-suppress LessSpecificImplementedReturnType
     */
    final public function jsonSerialize(): array
    {
        return [
            'token' => $this->token,
            'exception' => json_decode(json_encode($this->exception, JSON_THROW_ON_ERROR), true),
            'throwException' => $this->throwException,
        ];
    }
}
