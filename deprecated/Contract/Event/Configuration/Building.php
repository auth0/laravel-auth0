<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Event\Configuration;

use Auth0\Laravel\Events\Configuration\BuildingConfigurationEventContract;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\Configuration\BuildingConfigurationEvent instead.
 *
 * @api
 */
interface Building extends BuildingConfigurationEventContract
{
}
