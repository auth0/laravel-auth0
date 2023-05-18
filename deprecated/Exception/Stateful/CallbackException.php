<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exception\Stateful;

use Auth0\Laravel\Exceptions\Controllers\CallbackControllerExceptionContract;
use Auth0\Laravel\Exceptions\ExceptionAbstract;

/**
 * Exception thrown when an error occurs in the SDK's callback handler.
 *
 * @codeCoverageIgnore
 * @api
 */
final class CallbackException extends ExceptionAbstract implements CallbackControllerExceptionContract
{
}
