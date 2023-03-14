<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Auth\User;

use Illuminate\Contracts\Auth\UserProvider;

interface Provider extends UserProvider
{
    /**
     * Returns the assigned user provider.
     */
    public function getRepository(): Repository;
}
