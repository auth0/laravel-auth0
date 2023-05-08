<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Auth;

use Auth0\Laravel\Contract\Entities\CredentialContract;
use Auth0\SDK\Contract\API\ManagementInterface;
use Auth0\SDK\Contract\Auth0Interface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\{Authenticatable, Guard, UserProvider};
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\{ContainerExceptionInterface, NotFoundExceptionInterface};

interface GuardContract extends Guard
{
    /**
     * Returns the currently authenticated user for the guard. If none is set, throws an exception.
     *
     * @throws AuthenticationException If no user is currently authenticated.
     */
    public function authenticate(): Authenticatable;

    /**
     * Returns whether there is a currently authenticated user for the guard.
     */
    public function check(): bool;

    /**
     * Clears the currently authenticated user for the guard. This will not clear a session, if one is set.
     */
    public function forgetUser(): self;

    /**
     * Returns the Guard's currently configured Credential, or null if no Credential is configured.
     */
    public function getCredential(): ?CredentialContract;

    public function getImposter(): ?CredentialContract;

    public function getImposterSource(): ?int;

    /**
     * Returns the Guard's currently configured UserProvider.
     */
    public function getProvider(): UserProvider;

    /**
     * Returns a Laravel session store from the current Request context. Note that this will start a session if one is not already started.
     */
    public function getSession(): Session;

    /**
     * Returns whether there is no currently authenticated user for the guard.
     */
    public function guest(): bool;

    /**
     * Returns whether a credential has a specified scope, such as "read:users". Note that RBAC must be enabled for this to work.
     *
     * @param string             $scope      The scope to check for.
     * @param CredentialContract|null $credential Optional. The Credential to check. If omitted, the currently authenticated Credential will be used.
     */
    public function hasScope(
        string $scope,
        ?CredentialContract $credential = null,
    ): bool;

    /**
     * Returns whether a credential has a specified permission, such as "read:users". Note that RBAC must be enabled for this to work.
     *
     * @param string             $permission      The permission to check for.
     * @param CredentialContract|null $credential Optional. The Credential to check. If omitted, the currently authenticated Credential will be used.
     */
    public function hasPermission(
        string $permission,
        ?CredentialContract $credential = null,
    ): bool;

    /**
     * Returns whether there is a currently authenticated user for the guard.
     */
    public function hasUser(): bool;

    /**
     * Queries the /userinfo endpoint, updates the currently authenticated user for the guard, and returns a new Authenticatable user representing the updated user.
     */
    public function getRefreshedUser(): ?Authenticatable;

    /**
     * Returns the id of the currently authenticated user for the guard, if available.
     */
    public function id(): int | string | null;

    public function isImpersonating(): bool;

    /**
     * Clears the currently authenticated user for the guard. If a credential is set, it will be cleared. If a session is set, it will be cleared.
     */
    public function logout(): self;

    /**
     * Query the /userinfo endpoint and update the currently authenticated user for the guard.
     */
    public function refreshUser(): void;

    /**
     * Toggle the Guard's impersonation state. This should only be used by the Impersonate trait, and is not intended for use by end-users. It is public to allow for testing.
     *
     * @param bool               $impersonate Whether or not the Guard should be impersonating.
     * @param CredentialContract $credential
     * @param ?int               $source
     */
    public function setImpersonating(
        CredentialContract $credential,
        ?int $source = null,
    ): self;

    /**
     * Sets the currently authenticated user for the guard. This method will replace the current user of an existing credential, if one is set, or establish a new one. If an existing credential uses a session source, the session will be updated.
     *
     * @param Authenticatable $user The user to set as authenticated.
     */
    public function setUser(
        Authenticatable $user,
    ): void;

    public function stopImpersonating(): void;

    /**
     * Returns the currently authenticated user for the guard, if available.
     */
    public function user(): ?Authenticatable;

    /**
     * This method is not currently implemented, but is required by Laravel's Guard contract.
     *
     * @param array<mixed> $credentials
     */
    public function validate(
        array $credentials = [],
    ): bool;

    /**
     * This method is not currently implemented, but is required by Laravel's Guard contract.
     */
    public function viaRemember(): bool;

    /**
     * Discover and return any potential credentials from the request.
     */
    public function find(): ?CredentialContract;

    /**
     * Get an Auth0 PHP SDK instance.
     *
     * @throws BindingResolutionException  If the Auth0 class cannot be resolved.
     * @throws NotFoundExceptionInterface  If the Auth0 service cannot be found.
     * @throws ContainerExceptionInterface If the Auth0 service cannot be resolved.
     *
     * @return Auth0Interface Auth0 PHP SDK instance.
     */
    public function sdk(
        $reset = false,
    ): Auth0Interface;

    /**
     * Get an Auth0 Management API instance.
     */
    public function management(): ManagementInterface;
}
