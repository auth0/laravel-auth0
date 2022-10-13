<?php

declare(strict_types=1);

namespace Auth0\Laravel\Auth\User;

final class Provider implements \Auth0\Laravel\Contract\Auth\User\Provider, \Illuminate\Contracts\Auth\UserProvider
{
    /**
     * {@inheritdoc}
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function retrieveById($identifier): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function retrieveByToken($identifier, $token): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function retrieveByCredentials(array $credentials): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function validateCredentials(
        \Illuminate\Contracts\Auth\Authenticatable $user,
        array $credentials,
    ): bool {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function updateRememberToken(\Illuminate\Contracts\Auth\Authenticatable $user, $token): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(): \Auth0\Laravel\Contract\Auth\User\Repository
    {
        static $repository = null;

        if (null === $repository) {
            /**
             * @var string|null $configured
             */
            $configured = config('auth.providers.auth0.repository') ?? \Auth0\Laravel\Auth\User\Repository::class;
            $repository = app($configured);
        }

        return $repository;
    }
}
