<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Auth\User;

interface Provider
{
    /**
     * Returns the assigned user provider.
     */
    public function getRepository(): Repository;
}
