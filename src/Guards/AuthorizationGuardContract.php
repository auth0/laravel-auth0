<?php

declare(strict_types=1);

namespace Auth0\Laravel\Guards;

use Auth0\Laravel\Entities\CredentialEntityContract;

/**
 * @api
 */
interface AuthorizationGuardContract extends GuardContract
{
    /**
     * Get a credential candidate from a provided access token.
     *
     * @return null|CredentialEntityContract Credential object if a valid token is found, null otherwise.
     */
    public function findToken(): ?CredentialEntityContract;

    public function login(
        ?CredentialEntityContract $credential,
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

    public function setCredential(
        ?CredentialEntityContract $credential = null,
    ): self;

    /**
     * Searches for an available credential from a specified source in the current request context.
     */
    public function find(): ?CredentialEntityContract;
}
