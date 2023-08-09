<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @internal
 *
 * @api
 */
abstract class TokenVerificationSucceededAbstract extends EventAbstract
{
    /**
     * @param string $token   JSON Web Token that was verified.
     * @param array  $payload The decoded contents of the verified JSON Web Token.
     */
    public function __construct(
        public string $token,
        public array $payload,
    ) {
    }

    /**
     * @psalm-suppress LessSpecificImplementedReturnType
     *
     * @return array{token: string, payload: mixed[]}
     */
    final public function jsonSerialize(): array
    {
        return [
            'token' => $this->token,
            'payload' => $this->payload,
        ];
    }
}
