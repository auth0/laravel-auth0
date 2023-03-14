<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exception;

use Auth0\Laravel\Contract\Exception\SessionException as SessionExceptionContract;
use Auth0\SDK\Exception\Auth0Exception;
use Exception;

/**
 * @codeCoverageIgnore
 */
final class SessionException extends Exception implements Auth0Exception, SessionExceptionContract
{
    public const LARAVEL_SESSION_INACCESSIBLE = 'The Laravel session store is inaccessible.';
}
