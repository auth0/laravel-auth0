<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exceptions\Controllers;

use Auth0\Laravel\Exceptions\ExceptionAbstract;

/**
 * Exception thrown when an error occurs in the SDK's callback handler.
 *
 * @codeCoverageIgnore
 * @api
 */
final class CallbackControllerException extends ExceptionAbstract implements CallbackControllerExceptionContract
{
}
