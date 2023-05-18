<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller\Stateful;

use Auth0\Laravel\Controllers\{LogoutControllerAbstract, LogoutControllerContract};

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Controllers\LogoutController instead.
 *
 * @api
 */
final class Logout extends LogoutControllerAbstract implements LogoutControllerContract
{
}
