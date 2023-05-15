<?php

declare(strict_types=1);

namespace Auth0\Laravel\Entities;

use Illuminate\Contracts\Auth\Authenticatable;
use JsonSerializable;

/**
 * @api
 */
abstract class CredentialEntityAbstract implements CredentialEntityContract, JsonSerializable
{
    /**
     * @param null|Authenticatable $user                  The user entity this credential represents.
     * @param null|string          $idToken               The ID token for this credential.
     * @param null|string          $accessToken           The access token for this credential.
     * @param null|array<string>   $accessTokenScope      The access token scope for this credential.
     * @param null|int             $accessTokenExpiration The access token expiration for this credential.
     * @param null|string          $refreshToken          The refresh token for this credential.
     * @param null|array<string>   $accessTokenDecoded    The decoded access token for this credential.
     */
    public function __construct(
        private ?Authenticatable $user = null,
        private ?string $idToken = null,
        private ?string $accessToken = null,
        private ?array $accessTokenScope = null,
        private ?int $accessTokenExpiration = null,
        private ?string $refreshToken = null,
        private ?array $accessTokenDecoded = null,
    ) {
    }

    final public function clear(): self
    {
        $this->user = null;
        $this->idToken = null;
        $this->accessToken = null;
        $this->accessTokenDecoded = null;
        $this->accessTokenScope = null;
        $this->accessTokenExpiration = null;
        $this->refreshToken = null;

        return $this;
    }

    final public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     */
    final public function getAccessTokenDecoded(): ?array
    {
        return $this->accessTokenDecoded;
    }

    final public function getAccessTokenExpiration(): ?int
    {
        return $this->accessTokenExpiration;
    }

    final public function getAccessTokenExpired(): ?bool
    {
        $expires = $this->getAccessTokenExpiration();

        if (null === $expires || $expires <= 0) {
            return null;
        }

        return time() >= $expires;
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     */
    final public function getAccessTokenScope(): ?array
    {
        return $this->accessTokenScope;
    }

    final public function getIdToken(): ?string
    {
        return $this->idToken;
    }

    final public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    final public function getUser(): ?Authenticatable
    {
        return $this->user;
    }

    /**
     * @return array{user: false|string, idToken: null|string, accessToken: null|string, accessTokenDecoded: null|string[], accessTokenScope: null|string[], accessTokenExpiration: null|int, accessTokenExpired: null|bool, refreshToken: null|string}
     */
    final public function jsonSerialize(): mixed
    {
        return [
            'user' => json_encode($this->getUser(), JSON_FORCE_OBJECT),
            'idToken' => $this->getIdToken(),
            'accessToken' => $this->getAccessToken(),
            'accessTokenDecoded' => $this->getAccessTokenDecoded(),
            'accessTokenScope' => $this->getAccessTokenScope(),
            'accessTokenExpiration' => $this->getAccessTokenExpiration(),
            'accessTokenExpired' => $this->getAccessTokenExpired(),
            'refreshToken' => $this->getRefreshToken(),
        ];
    }

    final public function setAccessToken(
        ?string $accessToken = null,
    ): self {
        $this->accessToken = $accessToken;

        return $this;
    }

    final public function setAccessTokenDecoded(
        ?array $accessTokenDecoded = null,
    ): self {
        $this->accessTokenDecoded = $accessTokenDecoded;

        return $this;
    }

    final public function setAccessTokenExpiration(
        ?int $accessTokenExpiration = null,
    ): self {
        $this->accessTokenExpiration = $accessTokenExpiration;

        return $this;
    }

    final public function setAccessTokenScope(
        ?array $accessTokenScope = null,
    ): self {
        $this->accessTokenScope = $accessTokenScope;

        return $this;
    }

    final public function setIdToken(
        ?string $idToken = null,
    ): self {
        $this->idToken = $idToken;

        return $this;
    }

    final public function setRefreshToken(
        ?string $refreshToken = null,
    ): self {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    final public function setUser(
        ?Authenticatable $user = null,
    ): self {
        $this->user = $user;

        return $this;
    }
}
