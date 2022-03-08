<?php

declare(strict_types=1);

namespace Auth0\Laravel\Model\Stateless;

/**
 * This model class represents a user authorized by an Access Token.
 */
final class User extends \Auth0\Laravel\Model\User implements \Auth0\Laravel\Contract\Model\Stateless\User
{
}
