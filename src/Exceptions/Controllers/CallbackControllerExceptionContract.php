<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exceptions\Controllers;

use Auth0\Laravel\Exceptions\ExceptionContract;

/**
 * Exception thrown when an error occurs in the SDK's callback handler.
 *
 * @codeCoverageIgnore
 *
 * @api
 */
interface CallbackControllerExceptionContract extends ExceptionContract
{
    /**
     * @var string
     */
    public const MSG_API_RESPONSE = '%s: %s';
}
