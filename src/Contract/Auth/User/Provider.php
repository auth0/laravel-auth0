<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Auth\User;

interface Provider
{
    /**
     * \Auth0\Laravel\Contract\Auth\User\Provider constructor.
     *
     * @param  \Auth0\Laravel\Auth\User\Repository  $repository  A repository instance.
     */
    public function __construct(\Auth0\Laravel\Auth\User\Repository $repository);

    /**
     * Returns the assigned user provider.
     */
    public function getRepository(): \Auth0\Laravel\Contract\Auth\User\Repository;
}
