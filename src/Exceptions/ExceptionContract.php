<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exceptions;

use Auth0\SDK\Exception\Auth0Exception;

/**
 * Base exception for all Auth0 Laravel SDK exceptions.
 *
 * @codeCoverageIgnore
 *
 * @api
 */
interface ExceptionContract extends Auth0Exception
{
}
