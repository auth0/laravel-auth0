<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exception;

use Auth0\Laravel\Contract\Exception\AuthenticationException as AuthenticationExceptionContract;
use Auth0\SDK\Exception\Auth0Exception;
use Exception;

/**
 * @codeCoverageIgnore
 */
final class AuthenticationException extends Exception implements Auth0Exception, AuthenticationExceptionContract
{
}
