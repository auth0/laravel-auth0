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
     * Searches for an available credential from a specified source in the current request context.
     */
    public function find(): ?CredentialEntityContract;

    /**
     * Get a credential candidate from a provided access token.
     *
     * @return null|CredentialEntityContract Credential object if a valid token is found, null otherwise.
     */
    public function findToken(): ?CredentialEntityContract;

    public function login(
        ?CredentialEntityContract $credential,
    ): self;

    public function setCredential(
        ?CredentialEntityContract $credential = null,
    ): self;
}
