<?php

declare(strict_types=1);

namespace Auth0\Laravel\Guards;

use Auth0\Laravel\Entities\CredentialEntityContract;

/**
 * @api
 */
interface AuthenticationGuardContract extends GuardContract
{
    /**
     * Searches for an available credential from a specified source in the current request context.
     */
    public function find(): ?CredentialEntityContract;

    /**
     * Get a credential candidate from an Auth0-PHP SDK session.
     *
     * @return null|CredentialEntityContract Credential when a valid token is found, null otherwise.
     */
    public function findSession(): ?CredentialEntityContract;

    public function login(
        ?CredentialEntityContract $credential,
    ): self;

    public function pushState(
        ?CredentialEntityContract $credential = null,
    ): self;

    public function setCredential(
        ?CredentialEntityContract $credential = null,
    ): self;
}
