<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * @api
 */
interface UserRepositoryContract
{
    /**
     * Generate a stateless User instance from a parsed Access Token.
     *
     * @param array $user an array containing the raw Auth0 user data
     */
    public function fromAccessToken(array $user): ?Authenticatable;

    /**
     * Generate a stateful User instance from an available Auth0-PHP user session.
     *
     * @param array $user an array containing the raw Auth0 user data
     */
    public function fromSession(array $user): ?Authenticatable;
}
