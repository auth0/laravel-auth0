<?php

declare(strict_types=1);

namespace Auth0\Laravel\Auth\User;

final class Provider implements \Illuminate\Contracts\Auth\UserProvider
{
    private Repository $repository;

    /**
     * Auth0UserProvider constructor.
     *
     * @param \Auth0\Laravel\Auth\User\Repository $repository
     */
    public function __construct(
        Repository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * Returns a \Auth0\Laravel\Model\Stateless\User instance from an Id Token.
     */
    public function retrieveById(
        $identifier
    ): ?\Illuminate\Contracts\Auth\Authenticatable {
        if (! is_string($identifier)) {
            return null;
        }

        $decoded = app('auth0')->getSdk()->decode($identifier, null, null, null, null, null, null, \Auth0\SDK\Token::TYPE_ID_TOKEN)->toArray();
        $scope = $decoded['scope'] ?? '';

        // Process $identifier here ...
        return $this->repository->fromAccessToken(
            $decoded,
            null,
            $identifier,
            explode(' ', $scope),
            null,
            null,
            null,
        );
    }

    /**
     * Returns a \Auth0\Laravel\Model\Stateless\User instance from an Access Token.
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function retrieveByToken(
        $identifier,
        $token
    ): ?\Illuminate\Contracts\Auth\Authenticatable {
        $decoded = app('auth0')->getSdk()->decode($token, null, null, null, null, null, null, \Auth0\SDK\Token::TYPE_TOKEN)->toArray();
        $scope = $decoded['scope'] ?? '';

        return $this->repository->fromAccessToken(
            $decoded,
            null,
            $token,
            explode(' ', $scope),
            null,
            null,
            null,
        );
    }

    /**
     * Returns a \Auth0\Laravel\Model\Stateless\User instance translated from an Auth0-PHP SDK session.
     */
    public function retrieveByCredentials(
        array $credentials
    ): ?\Illuminate\Contracts\Auth\Authenticatable {
        return $this->repository->fromSession(
            $credentials['user'] ?? null,
            $credentials['idToken'] ?? null,
            $credentials['accessToken'] ?? null,
            $credentials['accessTokenScope'] ?? null,
            $credentials['accessTokenExpiration'] ?? null,
            $credentials['accessTokenExpired'] ?? null,
            $credentials['refreshToken'] ?? null,
        );
    }

    /**
     * Returns true if the provided $user's unique identifier matches the credentials payload.
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function validateCredentials(
        \Illuminate\Contracts\Auth\Authenticatable $user,
        array $credentials
    ): bool {
        return false;
    }

    /**
     * Method required by interface. Not supported.
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function updateRememberToken(
        \Illuminate\Contracts\Auth\Authenticatable $user,
        $token
    ): void {
    }
}
