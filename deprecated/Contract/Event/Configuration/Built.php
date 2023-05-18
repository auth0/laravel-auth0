<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Event\Configuration;

use Auth0\Laravel\Events\Configuration\BuiltConfigurationEventContract;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\Configuration\BuiltConfigurationEvent instead.
 *
 * @api
 */
interface Built extends BuiltConfigurationEventContract
{
}
