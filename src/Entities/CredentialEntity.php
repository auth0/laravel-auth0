<?php

declare(strict_types=1);

namespace Auth0\Laravel\Entities;

use Illuminate\Contracts\Auth\Authenticatable;
use JsonSerializable;

/**
 * An entity representing a user credential.
 *
 * @internal
 *
 * @api
 */
final class CredentialEntity extends CredentialEntityAbstract implements CredentialEntityContract, JsonSerializable
{
    public function clear(): self
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

    public function setAccessToken(
        ?string $accessToken = null,
    ): self {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function setAccessTokenDecoded(
        ?array $accessTokenDecoded = null,
    ): self {
        $this->accessTokenDecoded = $accessTokenDecoded;

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

    /**
     * Create a new Credential instance.
     *
     * @param null|Authenticatable $user                  The user entity this credential represents.
     * @param null|string          $idToken               The ID token for this credential.
     * @param null|string          $accessToken           The access token for this credential.
     * @param null|array<string>   $accessTokenScope      The access token scope for this credential.
     * @param null|int             $accessTokenExpiration The access token expiration for this credential.
     * @param null|string          $refreshToken          The refresh token for this credential.
     * @param null|array<string>   $accessTokenDecoded    The decoded access token for this credential.
     */
    public static function create(
        ?Authenticatable $user = null,
        ?string $idToken = null,
        ?string $accessToken = null,
        ?array $accessTokenScope = null,
        ?int $accessTokenExpiration = null,
        ?string $refreshToken = null,
        ?array $accessTokenDecoded = null,
    ): self {
        return new self(
            $user,
            $idToken,
            $accessToken,
            $accessTokenScope,
            $accessTokenExpiration,
            $refreshToken,
            $accessTokenDecoded,
        );
    }
}
