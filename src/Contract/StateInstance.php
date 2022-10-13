<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract;

use Illuminate\Contracts\Auth\Authenticatable;

interface StateInstance
{
    /**
     * Reset all attributes of the StateInstance to default values.
     */
    public function clear(): self;

    /**
     * Return the authenticated user context for the current request.
     */
    public function getUser(): ?Authenticatable;

    /**
     * Set the authenticated user context for the current request.
     *
     * @param  Authenticatable|null  $user  an authenticated user context
     */
    public function setUser(?Authenticatable $user): self;

    /**
     * Retrieve the decoded token data for the request context, if available.
     */
    public function getDecoded(): ?array;

    /**
     * Set the decoded token data for the request context.
     */
    public function setDecoded(?array $data): self;

    /**
     * Retrieve the id token for the request context, if available.
     */
    public function getIdToken(): ?string;

    /**
     * Set the id token for the request context.
     */
    public function setIdToken(?string $idToken): self;

    /**
     * Retrieve the access token for the request context, if available.
     */
    public function getAccessToken(): ?string;

    /**
     * Set the access token for the request context.
     */
    public function setAccessToken(?string $accessToken): self;

    /**
     * Retrieve the access token scopes for the request context, if available.
     */
    public function getAccessTokenScope(): ?array;

    /**
     * Set the access token scopes for the request context.
     */
    public function setAccessTokenScope(?array $accessTokenScope): self;

    /**
     * Retrieve the access token expiration timestamp for the request context, if available.
     */
    public function getAccessTokenExpiration(): ?int;

    /**
     * Set the access token expiration timestamp for the request context.
     */
    public function setAccessTokenExpiration(?int $accessTokenExpiration): self;

    /**
     * Retrieve the access token expiration state, if available.
     */
    public function getAccessTokenExpired(): ?bool;

    /**
     * Retrieve the refresh token for the request context, if available.
     */
    public function getRefreshToken(): ?string;

    /**
     * Set the refresh token for the request context, if available.
     *
     * @param  string  $refreshToken  refresh token returned from the code exchange
     */
    public function setRefreshToken(?string $refreshToken): self;
}
