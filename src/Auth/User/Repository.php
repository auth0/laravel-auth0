<?php

declare(strict_types=1);

namespace Auth0\Laravel\Auth\User;

final class Repository
{
    /**
     * Generate a \Auth0\Laravel\Model\Stateful\User instance from an available Auth0-PHP user session.
     *
     * @param array       $profile               An array containing the raw Auth0 user data.
     * @param string|null $idToken               An ID Token used by the user context. Null when unavailable.
     * @param string|null $accessToken           An Access Token used by the user context. Null when unavailable.
     * @param array|null  $accessTokenScope      An array of scopes requested/returned during authentication for the user context. Null when unavailable.
     * @param int|null    $accessTokenExpiration A unix timestamp representing when an access token expires, if available.
     * @param bool|null   $accessTokenExpired    Returns true if the access token has expired, if an expiration timestamp was available.
     * @param string|null $refreshToken          A Refresh Token used by the user context. Null when unavailable.
     */
    public function fromSession(
        array $profile,
        ?string $idToken,
        ?string $accessToken,
        ?array $accessTokenScope,
        ?int $accessTokenExpiration,
        ?bool $accessTokenExpired,
        ?string $refreshToken
    ): \Illuminate\Contracts\Auth\Authenticatable {
        return new \Auth0\Laravel\Model\Stateful\User(
            $profile,
            $idToken,
            $accessToken,
            $accessTokenScope,
            $accessTokenExpiration,
            $accessTokenExpired,
            $refreshToken
        );
    }

    /**
     * Generate a \Auth0\Laravel\Model\Stateful\User instance from a parsed Access Token.
     *
     * @param array       $profile               An array containing the raw Auth0 user data.
     * @param string|null $idToken               An ID Token used by the user context. Null when unavailable.
     * @param string|null $accessToken           An Access Token used by the user context. Null when unavailable.
     * @param array|null  $accessTokenScope      An array of scopes requested/returned during authentication for the user context. Null when unavailable.
     * @param int|null    $accessTokenExpiration A unix timestamp representing when an access token expires, if available.
     * @param bool|null   $accessTokenExpired    Returns true if the access token has expired, if an expiration timestamp was available.
     * @param string|null $refreshToken          A Refresh Token used by the user context. Null when unavailable.
     */
    public function fromAccessToken(
        array $profile,
        ?string $idToken,
        ?string $accessToken,
        ?array $accessTokenScope,
        ?int $accessTokenExpiration,
        ?bool $accessTokenExpired,
        ?string $refreshToken
    ): \Illuminate\Contracts\Auth\Authenticatable {
        return new \Auth0\Laravel\Model\Stateless\User(
            $profile,
            $idToken,
            $accessToken,
            $accessTokenScope,
            $accessTokenExpiration,
            $accessTokenExpired,
            $refreshToken
        );
    }

    /**
     * Generate a \Auth0\Laravel\Model\Stateful\User instance from a parsed ID Token.
     *
     * @param array       $profile               An array containing the raw Auth0 user data.
     * @param string|null $idToken               An ID Token used by the user context. Null when unavailable.
     * @param string|null $accessToken           An Access Token used by the user context. Null when unavailable.
     * @param array|null  $accessTokenScope      An array of scopes requested/returned during authentication for the user context. Null when unavailable.
     * @param int|null    $accessTokenExpiration A unix timestamp representing when an access token expires, if available.
     * @param bool|null   $accessTokenExpired    Returns true if the access token has expired, if an expiration timestamp was available.
     * @param string|null $refreshToken          A Refresh Token used by the user context. Null when unavailable.
     */
    public function fromIdToken(
        array $profile,
        ?string $idToken,
        ?string $accessToken,
        ?array $accessTokenScope,
        ?int $accessTokenExpiration,
        ?bool $accessTokenExpired,
        ?string $refreshToken
    ): \Illuminate\Contracts\Auth\Authenticatable {
        return new \Auth0\Laravel\Model\Stateless\User(
            $profile,
            $idToken,
            $accessToken,
            $accessTokenScope,
            $accessTokenExpiration,
            $accessTokenExpired,
            $refreshToken
        );
    }
}
