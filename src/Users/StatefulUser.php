<?php

declare(strict_types=1);

namespace Auth0\Laravel\Users;

/**
 * Model representing a user authenticated by a session.
 *
 * @api
 */
final class StatefulUser extends UserAbstract implements StatefulUserContract
{
    use UserTrait;
}
