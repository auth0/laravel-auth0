<?php

declare(strict_types=1);

namespace Auth0\Laravel;

final class StateInstance implements \Auth0\Laravel\Contract\StateInstance
{
    /**
     * An authenticated user context for the current request.
     */
    private ?\Illuminate\Contracts\Auth\Authenticatable $user = null;

    /**
     * Decoded token data from the request context.
     */
    private ?array $decoded;

    /**
     * ID Token for the request context, when available.
     */
    private ?string $idToken;

    /**
     * Access Token for the request context, when available.
     */
    private ?string $accessToken;

    /**
     * Access Token scopes for the request context, when available.
     */
    private ?array $accessTokenScope;

    /**
     * Access Token expiration timestamp for the request context, when available.
     */
    private ?int $accessTokenExpiration;

    /**
     * Refresh Token for the request context, when available.
     */
    private ?string $refreshToken;

    /**
     * @inheritdoc
     */
    public function clear(): self
    {
        $this->user = null;
        $this->decoded = null;
        $this->idToken = null;
        $this->accessToken = null;
        $this->accessTokenScope = null;
        $this->accessTokenExpiration = null;
        $this->refreshToken = null;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUser(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        return $this->user;
    }

    /**
     * @inheritdoc
     */
    public function setUser(
        ?\Illuminate\Contracts\Auth\Authenticatable $user
    ): self {
        $this->user = $user;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDecoded(): ?array
    {
        return $this->decoded;
    }

    /**
     * @inheritdoc
     */
    public function setDecoded(
        ?array $data
    ): self {
        $this->decoded = $data;
        return $this;
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
    public function setIdToken(
        ?string $idToken
    ): self {
        $this->idToken = $idToken;
        return $this;
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
    public function setAccessToken(
        ?string $accessToken
    ): self {
        $this->accessToken = $accessToken;
        return $this;
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
    public function setAccessTokenScope(
        ?array $accessTokenScope
    ): self {
        $this->accessTokenScope = $accessTokenScope;
        return $this;
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
    public function setAccessTokenExpiration(
        ?int $accessTokenExpiration
    ): self {
        $this->accessTokenExpiration = $accessTokenExpiration;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAccessTokenExpired(): ?bool
    {
        $expires = $this->getAccessTokenExpiration();

        if ($expires === null) {
            return null;
        }

        return time() >= $expires;
    }

    /**
     * @inheritdoc
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    /**
     * @inheritdoc
     */
    public function setRefreshToken(
        ?string $refreshToken
    ): self {
        $this->refreshToken = $refreshToken;
        return $this;
    }
}
