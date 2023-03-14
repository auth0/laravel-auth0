<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exception;

use Auth0\Laravel\Contract\Exception\GuardException as GuardExceptionContract;
use Auth0\SDK\Exception\Auth0Exception;
use Exception;

/**
 * @codeCoverageIgnore
 */
final class GuardException extends Exception implements Auth0Exception, GuardExceptionContract
{
}
