<?php

declare(strict_types=1);

namespace Auth0\Laravel\Model;

abstract class User implements \Illuminate\Contracts\Auth\Authenticatable
{
    /**
     * Profile data for the user context, when available.
     */
    private array $profile;

    /**
     * ID Token for the user context, when available.
     */
    private ?string $idToken;

    /**
     * Access Token for the user context, when available.
     */
    private ?string $accessToken;

    /**
     * Access Token scopes for the user context, when available.
     */
    private ?array $accessTokenScope;

    /**
     * Access Token expiration timestamp for the user context, when available.
     */
    private ?int $accessTokenExpiration;

    /**
     * Access Token expiration indicator for the user context, when available.
     */
    private ?bool $accessTokenExpired;

    /**
     * Refresh Token for the user context, when available.
     */
    private ?string $refreshToken;

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
    ) {
        $this->profile = $profile;
        $this->idToken = $idToken;
        $this->accessToken = $accessToken;
        $this->accessTokenScope = $accessTokenScope;
        $this->accessTokenExpiration = $accessTokenExpiration;
        $this->accessTokenExpired = $accessTokenExpired;
        $this->refreshToken = $refreshToken;
    }

    /**
     * Add a generic getter to get all the properties of the user.
     *
     * @return mixed|null Returns the related value, or null if not set.
     */
    public function __get(
        string $name
    ) {
        return array_key_exists($name, $this->profile) ? $this->profile[$name] : null;
    }

    /**
     * Return a JSON-encoded representation of the user.
     */
    public function __toString(): string
    {
        return json_encode($this->profile, JSON_THROW_ON_ERROR, 512);
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        if (isset($this->profile['sub'])) {
            return $this->profile['sub'];
        }

        return $this->profile['user_id'];
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        return '';
    }

    /**
     * Get the token value for the "remember me" session.
     */
    public function getRememberToken(): string
    {
        return '';
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function setRememberToken(
        $value
    ): void {
    }

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName(): string
    {
        return '';
    }

    /**
     * Return the profile data for the user context. Null when unavailable.
     */
    public function getProfile(): ?array
    {
        return $this->profile;
    }

    /**
     * Return the ID Token for the user context. Null when unavailable.
     */
    public function getIdToken(): ?string
    {
        return $this->idToken;
    }

    /**
     * Return the Access Token for the user context. Null when unavailable.
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * Return the Access Token's scope for the user context. Null when unavailable.
     */
    public function getAccessTokenScope(): ?array
    {
        return $this->accessTokenScope;
    }

    /**
     * Return the Access Token's expiration (represented as a unix timestamp) for the user context. Null when unavailable.
     */
    public function getAccessTokenExpiration(): ?int
    {
        return $this->accessTokenExpiration;
    }

    /**
     * Return true if the Access Token has expired for the user context. Null when unavailable.
     */
    public function getAccessTokenExpired(): ?bool
    {
        return $this->accessTokenExpired;
    }

    /**
     * Return the Refresh Token for the user context. Null when unavailable.
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }
}
