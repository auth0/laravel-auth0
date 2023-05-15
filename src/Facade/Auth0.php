<?php

declare(strict_types=1);

namespace Auth0\Laravel\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for the Auth0 SDK.
 *
 * @codeCoverageIgnore
 *
 * @api
 */
final class Auth0 extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'auth0';
    }
}
