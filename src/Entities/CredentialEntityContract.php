<?php

declare(strict_types=1);

namespace Auth0\Laravel\Entities;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * @api
 */
interface CredentialEntityContract extends EntityContract
{
    /**
     * Clear all values from this Credential instance.
     */
    public function clear(): self;

    /**
     * Get the access token for this credential.
     */
    public function getAccessToken(): ?string;

    /**
     * Get the decoded access token content for this credential.
     *
     * @return null|array<mixed>
     */
    public function getAccessTokenDecoded(): ?array;

    /**
     * Get the access token expiration for this credential.
     */
    public function getAccessTokenExpiration(): ?int;

    /**
     * Check if the access token for this credential has expired.
     */
    public function getAccessTokenExpired(): ?bool;

    /**
     * Get the access token scope for this credential.
     *
     * @return null|array<mixed>
     */
    public function getAccessTokenScope(): ?array;

    /**
     * Get the ID token for this credential.
     */
    public function getIdToken(): ?string;

    /**
     * Get the refresh token for this credential.
     */
    public function getRefreshToken(): ?string;

    /**
     * Get the user entity this credential represents.
     */
    public function getUser(): ?Authenticatable;

    /**
     * Set the access token for this credential.
     *
     * @param null|string $accessToken The access token for this credential.
     */
    public function setAccessToken(
        ?string $accessToken = null,
    ): self;

    /**
     * Set the decoded access token content for this credential.
     *
     * @param null|array<mixed> $accessTokenDecoded The decoded access token content for this credential.
     */
    public function setAccessTokenDecoded(
        ?array $accessTokenDecoded = null,
    ): self;

    /**
     * Set the access token expiration for this credential.
     *
     * @param null|int $accessTokenExpiration The access token expiration for this credential.
     */
    public function setAccessTokenExpiration(
        ?int $accessTokenExpiration = null,
    ): self;

    /**
     * Set the access token scope for this credential.
     *
     * @param null|array<mixed> $accessTokenScope The access token scope for this credential.
     */
    public function setAccessTokenScope(
        ?array $accessTokenScope = null,
    ): self;

    /**
     * Set the ID token for this credential.
     *
     * @param null|string $idToken The ID token for this credential.
     */
    public function setIdToken(
        ?string $idToken = null,
    ): self;

    /**
     * Set the refresh token for this credential.
     *
     * @param null|string $refreshToken The refresh token for this credential.
     */
    public function setRefreshToken(
        ?string $refreshToken = null,
    ): self;

    /**
     * Set the user entity this credential represents.
     *
     * @param null|Authenticatable $user The user entity this credential represents.
     */
    public function setUser(
        ?Authenticatable $user = null,
    ): self;
}
