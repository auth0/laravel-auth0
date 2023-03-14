<?php

declare(strict_types=1);

namespace Auth0\Laravel\Model\Stateful;

use Auth0\Laravel\Contract\Model\Stateful\User as UserContract;
use Auth0\Laravel\Model\User as GenericUser;

/**
 * This model class represents a user authenticated by an Auth0 PHP session.
 */
final class User extends GenericUser implements UserContract
{
}
