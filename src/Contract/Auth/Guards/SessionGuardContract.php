<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Auth\Guards;

use Auth0\Laravel\Contract\Auth\GuardContract;
use Auth0\Laravel\Contract\Entities\CredentialContract;

interface SessionGuardContract extends GuardContract
{
    /**
     * Get a credential candidate from an Auth0-PHP SDK session.
     *
     * @return null|CredentialContract Credential when a valid token is found, null otherwise.
     */
    public function findSession(): ?CredentialContract;

    public function login(
        ?CredentialContract $credential,
    ): self;

    public function pushState(
        ?CredentialContract $credential = null,
    ): self;

    public function setCredential(
        ?CredentialContract $credential = null,
    ): self;
}
