<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @internal
 *
 * @api
 */
abstract class TokenVerificationAttemptingAbstract extends EventAbstract
{
    /**
     * @param string $token Encoded JSON Web Token that will be verification.
     */
    public function __construct(
        public string $token,
    ) {
    }

    /**
     * @psalm-suppress LessSpecificImplementedReturnType
     *
     * @return array{token: string}
     */
    final public function jsonSerialize(): array
    {
        return [
            'token' => $this->token,
        ];
    }
}
