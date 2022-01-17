<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Auth\User;

interface Provider
{
    /**
     * Auth0UserProvider constructor.
     *
     * @param \Auth0\Laravel\Auth\User\Repository $repository A repository instance.
     */
    public function __construct(
        \Auth0\Laravel\Auth\User\Repository $repository
    );

    /**
     * Returns a \Auth0\Laravel\Model\Stateless\User instance from an Id Token.
     *
     * @param string $identifier A string representing the encoded Id Token.
     */
    public function retrieveById(
        $identifier
    ): ?\Illuminate\Contracts\Auth\Authenticatable;

    /**
     * Returns a \Auth0\Laravel\Model\Stateless\User instance from an Access Token.
     *
     * @param mixed  $identifier Unused in this provider.
     * @param string $token      A string representing the encoded Access Token.
     */
    public function retrieveByToken(
        $identifier,
        $token
    ): ?\Illuminate\Contracts\Auth\Authenticatable;

    /**
     * Returns a \Auth0\Laravel\Model\Stateless\User instance translated from an Auth0-PHP SDK session.
     */
    public function retrieveByCredentials(
        array $credentials
    ): ?\Illuminate\Contracts\Auth\Authenticatable;

    /**
     * Returns true if the provided $user's unique identifier matches the credentials payload.
     */
    public function validateCredentials(
        \Illuminate\Contracts\Auth\Authenticatable $user,
        array $credentials
    ): bool;

    /**
     * Method required by interface. Not supported.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user  User context.
     * @param string                                     $token A string representing the remember token.
     */
    public function updateRememberToken(
        \Illuminate\Contracts\Auth\Authenticatable $user,
        $token
    ): void;
}
