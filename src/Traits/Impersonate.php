<?php

declare(strict_types=1);

namespace Auth0\Laravel\Traits;

use Auth0\Laravel\Entities\CredentialEntityContract;
use Auth0\Laravel\Guards\GuardContract;
use Auth0\Laravel\Users\ImposterUser;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Pretend to be an authenticated user or a bearer token-established stateless user for the request. Only intended for unit testing.
 *
 * @api
 */
trait Impersonate
{
    /**
     * Pretend to be an authenticated user or a bearer token-established stateless user for the request. Only intended for unit testing.
     *
     * @param CredentialEntityContract $credential The Credential to impersonate.
     * @param null|int                 $source     The source of the Credential.
     * @param null|string              $guard      The guard to impersonate with.
     *
     * @return $this The current test case instance.
     */
    public function impersonate(
        CredentialEntityContract $credential,
        ?int $source = null,
        ?string $guard = null,
    ): self {
        if (GuardContract::SOURCE_SESSION === $source || null === $source) {
            $this->impersonateSession($credential, $guard);
        }

        if (GuardContract::SOURCE_TOKEN === $source || null === $source) {
            $this->impersonateToken($credential, $guard);
        }

        return $this;
    }

    /**
     * Pretend to be an authenticated user for the request. Only intended for unit testing.
     *
     * @param CredentialEntityContract $credential The Credential to impersonate.
     * @param null|string              $guard      The guard to impersonate with.
     *
     * @return $this The current test case instance.
     */
    public function impersonateSession(
        CredentialEntityContract $credential,
        ?string $guard = null,
    ): self {
        $instance = auth()->guard($guard);
        $user = $credential->getUser() ?? new ImposterUser([]);

        if ($instance instanceof GuardContract) {
            $instance->setImpersonating($credential, GuardContract::SOURCE_SESSION);
        }

        return $this->actingAs($user, $guard);
    }

    /**
     * Pretend to be a bearer token-established stateless user for the request. Only intended for unit testing.
     *
     * @param CredentialEntityContract $credential The Credential to impersonate.
     * @param null|string              $guard      The guard to impersonate with.
     *
     * @return $this The current test case instance.
     */
    public function impersonateToken(
        CredentialEntityContract $credential,
        ?string $guard = null,
    ): self {
        $instance = auth()->guard($guard);
        $user = $credential->getUser() ?? new ImposterUser([]);

        if ($instance instanceof GuardContract) {
            $instance->setImpersonating($credential, GuardContract::SOURCE_TOKEN);
        }

        return $this->actingAs($user, $guard);
    }

    abstract public function actingAs(Authenticatable $user, $guard = null);
}
