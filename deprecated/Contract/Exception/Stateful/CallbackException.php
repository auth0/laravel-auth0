<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Exception\Stateful;

use Auth0\Laravel\Exceptions\Controllers\CallbackControllerExceptionContract;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Exceptions\Controllers\CallbackControllerException instead.
 *
 * @api
 */
interface CallbackException extends CallbackControllerExceptionContract
{
}
