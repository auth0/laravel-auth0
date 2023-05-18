<?php

declare(strict_types=1);

namespace Auth0\Laravel\Model\Stateful;

use Auth0\Laravel\Users\StatefulUserContract;
use Auth0\Laravel\Users\UserAbstract;
use Auth0\Laravel\Users\UserTrait;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Users\StatefulUser instead.
 * @api
 */
final class User extends UserAbstract implements StatefulUserContract
{
    use UserTrait;
}
