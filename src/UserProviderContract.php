<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Illuminate\Contracts\Auth\UserProvider;

/**
 * @api
 */
interface UserProviderContract extends UserProvider
{
    /**
     * Returns the assigned user provider.
     */
    public function getRepository(): UserRepositoryContract;
}
