<?php

declare(strict_types=1);

namespace Auth0\Laravel\Model;

use Auth0\Laravel\Users\{UserAbstract, UserContract};

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Users\UserAbstract instead.
 *
 * @api
 */
abstract class User extends UserAbstract
{
}
