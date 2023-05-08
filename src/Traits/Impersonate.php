<?php

declare(strict_types=1);

namespace Auth0\Laravel\Traits;

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Contract\Auth\GuardContract;
use Auth0\Laravel\Contract\Entities\CredentialContract;
use Auth0\Laravel\Model\Stateful\User as Stateful;
use Auth0\Laravel\Model\Stateless\User as Stateless;
use Illuminate\Contracts\Auth\Authenticatable;

trait Impersonate
{
    /**
     * Pretend to be an authenticated user or a bearer token-established stateless user for the request. Only intended for unit testing.
     *
     * @param CredentialContract $credential The Credential to impersonate.
     * @param null|int           $source     The source of the Credential.
     * @param null|string        $guard      The guard to impersonate with.
     *
     * @return $this The current test case instance.
     */
    public function impersonate(
        CredentialContract $credential,
        ?int $source = null,
        ?string $guard = null,
    ): self {
        if (Guard::SOURCE_SESSION === $source || null === $source) {
            $this->impersonateSession($credential, $guard);
        }

        if (Guard::SOURCE_TOKEN === $source || null === $source) {
            $this->impersonateToken($credential, $guard);
        }

        return $this;
    }

    /**
     * Pretend to be an authenticated user for the request. Only intended for unit testing.
     *
     * @param CredentialContract $credential The Credential to impersonate.
     * @param null|string        $guard      The guard to impersonate with.
     *
     * @return $this The current test case instance.
     */
    public function impersonateSession(
        CredentialContract $credential,
        ?string $guard = null,
    ): self {
        $instance = auth()->guard($guard);
        $user = $credential->getUser() ?? new Stateful([]);

        if ($instance instanceof GuardContract) {
            $instance->setImpersonating($credential, Guard::SOURCE_SESSION);
        }

        return $this->actingAs($user, $guard);
    }

    /**
     * Pretend to be a bearer token-established stateless user for the request. Only intended for unit testing.
     *
     * @param CredentialContract $credential The Credential to impersonate.
     * @param null|string        $guard      The guard to impersonate with.
     *
     * @return $this The current test case instance.
     */
    public function impersonateToken(
        CredentialContract $credential,
        ?string $guard = null,
    ): self {
        $instance = auth()->guard($guard);
        $user = $credential->getUser() ?? new Stateless([]);

        if ($instance instanceof GuardContract) {
            $instance->setImpersonating($credential, Guard::SOURCE_TOKEN);
        }

        return $this->actingAs($user, $guard);
    }

    abstract public function actingAs(Authenticatable $user, $guard = null);
}
