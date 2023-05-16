<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Auth0\Laravel\Entities\InstanceEntityTrait;

/**
 * Auth0 Laravel SDK service provider. Provides access to the SDK's methods.
 *
 * @codeCoverageIgnore
 *
 * @deprecated 7.8.0 Use Auth0\Laravel\Service instead.
 *
 * @api
 */
final class Auth0 extends ServiceAbstract implements ServiceContract
{
    use InstanceEntityTrait;
}
