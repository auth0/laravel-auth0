<?php

declare(strict_types=1);

namespace Auth0\Laravel\Model;

abstract class User implements \Illuminate\Contracts\Auth\Authenticatable, \Auth0\Laravel\Contract\Model\User
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
     * @inheritdoc
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
     * @inheritdoc
     */
    public function __get(
        string $name
    ) {
        return array_key_exists($name, $this->profile) ? $this->profile[$name] : null;
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return json_encode($this->profile, JSON_THROW_ON_ERROR, 512);
    }

    /**
     * @inheritdoc
     */
    public function getAuthIdentifier()
    {
        if (isset($this->profile['sub'])) {
            return $this->profile['sub'];
        }

        return $this->profile['user_id'];
    }

    /**
     * @inheritdoc
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * @inheritdoc
     */
    public function getAuthPassword(): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getRememberToken(): string
    {
        return '';
    }

    /**
     * @inheritdoc
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function setRememberToken(
        $value
    ): void {
    }

    /**
     * @inheritdoc
     */
    public function getRememberTokenName(): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getProfile(): ?array
    {
        return $this->profile;
    }

    /**
     * @inheritdoc
     */
    public function getIdToken(): ?string
    {
        return $this->idToken;
    }

    /**
     * @inheritdoc
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * @inheritdoc
     */
    public function getAccessTokenScope(): ?array
    {
        return $this->accessTokenScope;
    }

    /**
     * @inheritdoc
     */
    public function getAccessTokenExpiration(): ?int
    {
        return $this->accessTokenExpiration;
    }

    /**
     * @inheritdoc
     */
    public function getAccessTokenExpired(): ?bool
    {
        return $this->accessTokenExpired;
    }

    /**
     * @inheritdoc
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }
}
