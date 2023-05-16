<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @api
 */
interface TokenVerificationSucceededContract extends EventContract
{
    /**
     * AuthenticationSucceeded constructor.
     *
     * @param string $token   a bearer JSON web token
     * @param array  $payload the bearer JSON web token's decoded payload
     */
    public function __construct(
        string $token,
        array $payload,
    );

    /**
     * Return the bearer JSON web token's decoded payload.
     */
    public function getPayload(): array;

    /**
     * Return the bearer JSON web token.
     */
    public function getToken(): string;
}
