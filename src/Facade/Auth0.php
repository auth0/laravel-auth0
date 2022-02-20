<?php

declare(strict_types=1);

namespace Auth0\Laravel\Facade;

/**
 * @codeCoverageIgnore
 */
final class Auth0 extends \Illuminate\Support\Facades\Facade
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
