<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller\Stateful;

use Auth0\Laravel\Controllers\LogoutControllerAbstract;
use Auth0\Laravel\Controllers\LogoutControllerContract;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Controllers\LogoutController instead.
 * @api
 */
final class LogoutController extends LogoutControllerAbstract implements LogoutControllerContract
{
}
