<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Auth0\Laravel\Users\{StatefulUser, StatelessUser};
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * User repository for the Auth0 Laravel SDK user provider.
 *
 * @internal
 *
 * @api
 */
final class UserRepository extends UserRepositoryAbstract implements UserRepositoryContract
{
    public function fromAccessToken(array $user): ?Authenticatable
    {
        return new StatelessUser($user);
    }

    public function fromSession(array $user): ?Authenticatable
    {
        return new StatefulUser($user);
    }
}
