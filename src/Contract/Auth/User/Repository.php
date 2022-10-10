<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Auth\User;

interface Repository
{
    /**
     * Generate a \Auth0\Laravel\Model\Stateful\User instance from an available Auth0-PHP user session.
     *
     * @param array $user An array containing the raw Auth0 user data.
     */
    public function fromSession(array $user): ?\Illuminate\Contracts\Auth\Authenticatable;

    /**
     * Generate a \Auth0\Laravel\Model\Stateful\User instance from a parsed Access Token.
     *
     * @param array $user An array containing the raw Auth0 user data.
     */
    public function fromAccessToken(array $user): ?\Illuminate\Contracts\Auth\Authenticatable;
}
