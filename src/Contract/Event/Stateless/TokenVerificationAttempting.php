<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Event\Stateless;

interface TokenVerificationAttempting
{
    /**
     * AuthenticationSucceeded constructor.
     *
     * @param  string  $token  a bearer JSON web token
     */
    public function __construct(string $token);

    /**
     * Overwrite the bearer JSON web token.
     *
     * @param  string  $token  a bearer JSON web token
     */
    public function setToken(string $token): self;

    /**
     * Return the bearer JSON web token
     */
    public function getToken(): string;
}
