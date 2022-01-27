<?php

declare(strict_types=1);

namespace Auth0\Laravel\Model\Stateful;

/**
 * This model class represents a user authenticated by an Auth0 PHP session.
 */
final class User extends \Auth0\Laravel\Model\User implements \Auth0\Laravel\Contract\Model\Stateful\User
{
}
