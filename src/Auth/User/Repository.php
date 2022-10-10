<?php

declare(strict_types=1);

namespace Auth0\Laravel\Auth\User;

final class Repository implements \Auth0\Laravel\Contract\Auth\User\Repository
{
    /**
     * {@inheritdoc}
     */
    public function fromSession(array $user): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        return new \Auth0\Laravel\Model\Stateful\User($user);
    }

    /**
     * {@inheritdoc}
     */
    public function fromAccessToken(array $user): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        return new \Auth0\Laravel\Model\Stateless\User($user);
    }
}
