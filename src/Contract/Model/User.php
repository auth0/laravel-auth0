<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Model;

interface User
{
    /**
     * \Auth0\Laravel\Model\User constructor.
     */
    public function __construct(
        array $profile,
        ?string $idToken,
        ?string $accessToken,
        ?array $accessTokenScope,
        ?int $accessTokenExpiration,
        ?bool $accessTokenExpired,
        ?string $refreshToken
    );

    /**
     * Add a generic getter to get all the properties of the user.
     *
     * @return mixed|null Returns the related value, or null if not set.
     */
    public function __get(
        string $name
    );

    /**
     * Return a JSON-encoded representation of the user.
     */
    public function __toString(): string;

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier();

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName();

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string;

    /**
     * Get the token value for the "remember me" session.
     */
    public function getRememberToken(): string;

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     */
    public function setRememberToken(
        $value
    ): void;

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName(): string;

    /**
     * Return the profile data for the user context. Null when unavailable.
     */
    public function getProfile(): ?array;

    /**
     * Return the ID Token for the user context. Null when unavailable.
     */
    public function getIdToken(): ?string;

    /**
     * Return the Access Token for the user context. Null when unavailable.
     */
    public function getAccessToken(): ?string;

    /**
     * Return the Access Token's scope for the user context. Null when unavailable.
     */
    public function getAccessTokenScope(): ?array;

    /**
     * Return the Access Token's expiration (represented as a unix timestamp) for the user context. Null when unavailable.
     */
    public function getAccessTokenExpiration(): ?int;

    /**
     * Return true if the Access Token has expired for the user context. Null when unavailable.
     */
    public function getAccessTokenExpired(): ?bool;

    /**
     * Return the Refresh Token for the user context. Null when unavailable.
     */
    public function getRefreshToken(): ?string;
}
