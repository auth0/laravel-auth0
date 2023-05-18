<?php

declare(strict_types=1);

namespace Auth0\Laravel\Entities;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * @api
 */
abstract class CredentialEntityAbstract extends EntityAbstract
{
    /**
     * @param null|Authenticatable $user                  The user entity this credential represents.
     * @param null|string          $idToken               The ID token for this credential.
     * @param null|string          $accessToken           The access token for this credential.
     * @param null|array<mixed>    $accessTokenScope      The access token scope for this credential.
     * @param null|int             $accessTokenExpiration The access token expiration for this credential.
     * @param null|string          $refreshToken          The refresh token for this credential.
     * @param null|array<mixed>    $accessTokenDecoded    The decoded access token for this credential.
     */
    public function __construct(
        protected ?Authenticatable $user = null,
        protected ?string $idToken = null,
        protected ?string $accessToken = null,
        protected ?array $accessTokenScope = null,
        protected ?int $accessTokenExpiration = null,
        protected ?string $refreshToken = null,
        protected ?array $accessTokenDecoded = null,
    ) {
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
     * @return array{user: false|string, idToken: null|string, accessToken: null|string, accessTokenDecoded: null|array<mixed>, accessTokenScope: null|array<mixed>, accessTokenExpiration: null|int, accessTokenExpired: null|bool, refreshToken: null|string}
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

    abstract public function clear(): self;

    abstract public function setAccessToken(
        ?string $accessToken = null,
    ): self;

    abstract public function setAccessTokenDecoded(
        ?array $accessTokenDecoded = null,
    ): self;

    abstract public function setAccessTokenExpiration(
        ?int $accessTokenExpiration = null,
    ): self;

    abstract public function setAccessTokenScope(
        ?array $accessTokenScope = null,
    ): self;

    abstract public function setIdToken(
        ?string $idToken = null,
    ): self;

    abstract public function setRefreshToken(
        ?string $refreshToken = null,
    ): self;

    abstract public function setUser(
        ?Authenticatable $user = null,
    ): self;
}
