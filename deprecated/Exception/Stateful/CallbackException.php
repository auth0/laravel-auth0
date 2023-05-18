<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exception\Stateful;

use Auth0\Laravel\Exceptions\Controllers\CallbackControllerExceptionContract;
use Auth0\Laravel\Exceptions\Controllers\CallbackControllerExceptionAbstract;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Exceptions\Controllers\CallbackControllerException instead.
 * @api
 */
final class CallbackControllerException extends CallbackControllerExceptionAbstract implements CallbackControllerExceptionContract
{
}

