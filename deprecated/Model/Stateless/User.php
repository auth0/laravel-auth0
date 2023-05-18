<?php

declare(strict_types=1);

namespace Auth0\Laravel\Model\Stateless;

use Auth0\Laravel\Users\{StatelessUserContract, UserAbstract, UserTrait};

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Users\StatelessUser instead.
 *
 * @api
 */
final class User extends UserAbstract implements StatelessUserContract
{
    use UserTrait;
}
