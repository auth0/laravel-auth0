<?php

declare(strict_types=1);

namespace Auth0\Laravel\Auth\User;

use Auth0\Laravel\Contract\Auth\User\Repository as RepositoryContract;
use Auth0\Laravel\Model\Stateful\User as StatefulUser;
use Auth0\Laravel\Model\Stateless\User as StatelessUser;
use Illuminate\Contracts\Auth\Authenticatable;

final class Repository implements RepositoryContract
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
