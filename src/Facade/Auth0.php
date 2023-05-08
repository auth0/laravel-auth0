<?php

declare(strict_types=1);

namespace Auth0\Laravel\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @codeCoverageIgnore
 */
final class Auth0 extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'auth0';
    }
}
