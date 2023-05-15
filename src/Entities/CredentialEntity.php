<?php

declare(strict_types=1);

namespace Auth0\Laravel\Entities;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * An entity representing a user credential.
 *
 * @internal
 *
 * @api
 */
final class CredentialEntity extends CredentialEntityAbstract
{
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
