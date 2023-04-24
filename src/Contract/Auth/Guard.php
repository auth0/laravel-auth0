<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Auth;

use Auth0\Laravel\Contract\Entities\Credential;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\{Authenticatable, Guard as GuardContract, UserProvider};
use Illuminate\Contracts\Session\Session;

interface Guard extends GuardContract
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
     * Searches for an available credential from a specified source in the current request context.
     *
     * @param int $source The source to search for a credential in. One of the Guard::SOURCE_* constants.
     */
    public function find(
        int $source,
    ): ?Credential;

    /**
     * Clears the currently authenticated user for the guard. This will not clear a session, if one is set.
     */
    public function forgetUser(): self;

    /**
     * Returns the Guard's currently configured Credential, or null if no Credential is configured.
     */
    public function getCredential(): ?Credential;

    /**
     * Returns the Guard's currently configured credential source, or null if no source is configured. One of the Guard::SOURCE_* constants.
     */
    public function getCredentialSource(): ?int;

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
     * Returns whether a provided credential has a specified scope.
     *
     * @param string     $scope      The scope to check for.
     * @param Credential $credential The Credential to check.
     */
    public function hasScope(
        string $scope,
        Credential $credential,
    ): bool;

    /**
     * Returns whether there is a currently authenticated user for the guard.
     */
    public function hasUser(): bool;

    /**
     * Returns the id of the currently authenticated user for the guard, if available.
     */
    public function id(): int | string | null;

    /**
     * Sets the currently authenticated user for the guard.
     *
     * @param null|Credential $credential The Credential to set.
     * @param null|int        $source     The source of the Credential.
     */
    public function login(
        ?Credential $credential,
        ?int $source = null,
    ): self;

    /**
     * Clears the currently authenticated user for the guard. If a credential is set, it will be cleared. If a session is set, it will be cleared.
     */
    public function logout(): self;

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

    /**
     * Sets the Guard's currently configured Credential and source.
     *
     * @param null|Credential $credential The Credential to set.
     * @param null|int        $source     The source of the Credential.
     */
    public function setCredential(
        ?Credential $credential,
        ?int $source = null,
    ): self;

    /**
     * Toggle the Guard's impersonation state. This should only be used by the Impersonate trait, and is not intended for use by end-users. It is public to allow for testing.
     *
     * @param bool $impersonate Whether or not the Guard should be impersonating.
     */
    public function setImpersonating(
        bool $impersonate = false,
    ): self;

    /**
     * Sets the currently authenticated user for the guard. This method will replace the current user of an existing credential, if one is set, or establish a new one. If an existing credential uses a session source, the session will be updated.
     *
     * @param Authenticatable $user The user to set as authenticated.
     */
    public function setUser(
        Authenticatable $user,
    ): void;

    /**
     * Query the /userinfo endpoint and update the currently authenticated user for the guard.
     */
    public function refreshUser(): void;

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
}
