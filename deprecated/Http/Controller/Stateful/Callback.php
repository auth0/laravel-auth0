<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller\Stateful;

use Auth0\Laravel\Controllers\{CallbackControllerAbstract, CallbackControllerContract};

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Controllers\CallbackController instead.
 *
 * @api
 */
final class Callback extends CallbackControllerAbstract implements CallbackControllerContract
{
}
