<?php

declare(strict_types=1);

namespace Auth0\Laravel\Auth\User;

final class Repository
{
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
