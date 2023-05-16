<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * User repository for the Auth0 Laravel SDK user provider.
 *
 * @api
 */
abstract class UserRepositoryAbstract
{
    abstract public function fromAccessToken(array $user): ?Authenticatable;

    abstract public function fromSession(array $user): ?Authenticatable;
}
