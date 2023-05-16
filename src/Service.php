<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Auth0\Laravel\Entities\InstanceEntityTrait;

/**
 * Auth0 Laravel SDK service provider. Provides access to the SDK's methods.
 *
 * @api
 */
final class Service extends ServiceAbstract implements ServiceContract
{
    use InstanceEntityTrait;
}
