<?php

declare(strict_types=1);

namespace Auth0\Laravel\Users;

/**
 * Model representing an "imposter" user, assigned using one of the unit testing traits. Only intended for unit tests.
 *
 * @api
 */
final class ImposterUser extends UserAbstract implements ImposterUserContract
{
    use UserTrait;
}
