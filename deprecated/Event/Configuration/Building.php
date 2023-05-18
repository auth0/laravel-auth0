<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Configuration;

use Auth0\Laravel\Events\Configuration\BuildingConfigurationEventAbstract;
use Auth0\Laravel\Events\Configuration\BuiltConfigurationEventContract;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\Configuration\BuildingConfigurationEvent instead
 *
 * @api
 */
final class Building extends BuildingConfigurationEventAbstract implements BuiltConfigurationEventContract
{
}
