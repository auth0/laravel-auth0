<?php

declare(strict_types=1);

namespace Auth0\Laravel\Model\Stateless;

use Auth0\Laravel\Contract\Model\Stateless\User as UserContract;
use Auth0\Laravel\Model\User as GenericUser;

/**
 * This model class represents a user authorized by an Access Token.
 */
final class User extends GenericUser implements UserContract
{
}
