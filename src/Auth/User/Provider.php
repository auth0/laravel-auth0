<?php

declare(strict_types=1);

namespace Auth0\Laravel\Auth\User;

final class Provider implements \Illuminate\Contracts\Auth\UserProvider, \Auth0\Laravel\Contract\Auth\User\Provider
{
    /**
     * A repository instance.
     */
    private \Auth0\Laravel\Contract\Auth\User\Repository $repository;

    /**
     * @inheritdoc
     */
    public function __construct(
        \Auth0\Laravel\Contract\Auth\User\Repository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function retrieveById(
        $identifier
    ): ?\Illuminate\Contracts\Auth\Authenticatable {
        if (! is_string($identifier)) {
            return null;
        }

        try {
            $decoded = app('auth0')->getSdk()->decode($identifier, null, null, null, null, null, null, \Auth0\SDK\Token::TYPE_ID_TOKEN)->toArray();
        } catch (\Auth0\SDK\Exception\InvalidTokenException $invalidToken) {
            $decoded = null;
        }

        if ($decoded !== null) {
            $scope = $decoded['scope'] ?? '';
            $exp = $decoded['exp'] ?? null;
            $expired = time() > $exp;

            // Process $identifier here ...
            return $this->repository->fromAccessToken(
                $decoded,
                null,
                $identifier,
                explode(' ', $scope),
                $exp,
                $expired,
                null,
            );
        }

        return null;
    }

    /**
     * @inheritdoc
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function retrieveByToken(
        $identifier,
        $token
    ): ?\Illuminate\Contracts\Auth\Authenticatable {
        try {
            $decoded = app('auth0')->getSdk()->decode($token, null, null, null, null, null, null, \Auth0\SDK\Token::TYPE_TOKEN)->toArray();
        } catch (\Auth0\SDK\Exception\InvalidTokenException $invalidToken) {
            $decoded = null;
        }

        if ($decoded !== null) {
            $scope = $decoded['scope'] ?? '';
            $exp = $decoded['exp'] ?? null;
            $expired = time() > $exp;

            return $this->repository->fromAccessToken(
                $decoded,
                null,
                $token,
                explode(' ', $scope),
                $exp,
                $expired,
                null,
            );
        }

        return null;
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
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
     * @inheritdoc
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function updateRememberToken(
        \Illuminate\Contracts\Auth\Authenticatable $user,
        $token
    ): void {
    }
}
