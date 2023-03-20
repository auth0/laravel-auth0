<?php

declare(strict_types=1);

namespace Auth0\Laravel\Entities;

use Auth0\Laravel\Contract\Entities\Credential as CredentialContract;
use Illuminate\Contracts\Auth\Authenticatable;

final class Credential implements CredentialContract
{
    /**
     * @param null|Authenticatable $user                  The user entity this credential represents.
     * @param null|string          $idToken               The ID token for this credential.
     * @param null|string          $accessToken           The access token for this credential.
     * @param null|array<string>   $accessTokenScope      The access token scope for this credential.
     * @param null|int             $accessTokenExpiration The access token expiration for this credential.
     * @param null|string          $refreshToken          The refresh token for this credential.
     */
    public function __construct(
        private ?Authenticatable $user = null,
        private ?string $idToken = null,
        private ?string $accessToken = null,
        private ?array $accessTokenScope = null,
        private ?int $accessTokenExpiration = null,
        private ?string $refreshToken = null,
    ) {
    }

    public function clear(): self
    {
        $this->user                  = null;
        $this->idToken               = null;
        $this->accessToken           = null;
        $this->accessTokenScope      = null;
        $this->accessTokenExpiration = null;
        $this->refreshToken          = null;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getAccessTokenExpiration(): ?int
    {
        return $this->accessTokenExpiration;
    }

    public function getAccessTokenExpired(): ?bool
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
    public function getAccessTokenScope(): ?array
    {
        return $this->accessTokenScope;
    }

    public function getIdToken(): ?string
    {
        return $this->idToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getUser(): ?Authenticatable
    {
        return $this->user;
    }

    /**
     * @return array{user: false|string, idToken: null|string, accessToken: null|string, accessTokenScope: null|string[], accessTokenExpiration: null|int, accessTokenExpired: null|bool, refreshToken: null|string}
     */
    public function jsonSerialize(): mixed
    {
        return [
            'user'                  => json_encode($this->getUser(), JSON_FORCE_OBJECT),
            'idToken'               => $this->getIdToken(),
            'accessToken'           => $this->getAccessToken(),
            'accessTokenScope'      => $this->getAccessTokenScope(),
            'accessTokenExpiration' => $this->getAccessTokenExpiration(),
            'accessTokenExpired'    => $this->getAccessTokenExpired(),
            'refreshToken'          => $this->getRefreshToken(),
        ];
    }

    public function setAccessToken(
        ?string $accessToken = null,
    ): self {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function setAccessTokenExpiration(
        ?int $accessTokenExpiration = null,
    ): self {
        $this->accessTokenExpiration = $accessTokenExpiration;

        return $this;
    }

    public function setAccessTokenScope(
        ?array $accessTokenScope = null,
    ): self {
        $this->accessTokenScope = $accessTokenScope;

        return $this;
    }

    public function setIdToken(
        ?string $idToken = null,
    ): self {
        $this->idToken = $idToken;

        return $this;
    }

    public function setRefreshToken(
        ?string $refreshToken = null,
    ): self {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function setUser(
        ?Authenticatable $user = null,
    ): self {
        $this->user = $user;

        return $this;
    }

    public static function create(
        ?Authenticatable $user = null,
        ?string $idToken = null,
        ?string $accessToken = null,
        ?array $accessTokenScope = null,
        ?int $accessTokenExpiration = null,
        ?string $refreshToken = null,
    ): self {
        return new self(
            $user,
            $idToken,
            $accessToken,
            $accessTokenScope,
            $accessTokenExpiration,
            $refreshToken,
        );
    }
}
