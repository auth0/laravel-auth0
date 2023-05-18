<?php

declare(strict_types=1);

namespace Auth0\Laravel\Users;

/**
 * Model representing a user derived from an access token.
 *
 * @api
 */
final class StatelessUser extends UserAbstract implements StatelessUserContract
{
    use UserTrait;
}
