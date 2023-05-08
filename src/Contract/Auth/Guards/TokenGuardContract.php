<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Auth\Guards;

use Auth0\Laravel\Contract\Auth\GuardContract;
use Auth0\Laravel\Contract\Entities\CredentialContract;

interface TokenGuardContract extends GuardContract
{
    /**
     * Get a credential candidate from a provided access token.
     *
     * @return null|CredentialContract Credential object if a valid token is found, null otherwise.
     */
    public function findToken(): ?CredentialContract;

    public function login(
        ?CredentialContract $credential,
    ): self;

    public function setCredential(
        ?CredentialContract $credential = null,
    ): self;

    /**
     * Processes a JWT token and returns the decoded token, or null if the token is invalid.
     *
     * @param string $token The JWT token to process.
     *
     * @return null|array<mixed>
     */
    public function processToken(
        string $token,
    ): ?array;
}
