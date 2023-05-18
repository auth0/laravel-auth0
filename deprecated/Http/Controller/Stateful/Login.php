<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller\Stateful;

use Auth0\Laravel\Controllers\{LoginControllerAbstract, LoginControllerContract};

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Controllers\LoginController instead.
 *
 * @api
 */
final class Login extends LoginControllerAbstract implements LoginControllerContract
{
}
